<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\SiteStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class SiteController extends Controller
{
    // Affiche la page d'ajout de site et la liste des sites
    public function index()
    {
        $sites = Site::all();
        $offlineStatuses = SiteStatus::where('status', false)
        ->orderBy('created_at', 'desc')
        ->get()
        ->groupBy(function($status) {
            return $status->created_at->format('Y-m-d'); // Grouper par date
        });
        return view('sites.index',['navbar_active'=>'analyse'], compact('sites','offlineStatuses'));
    }

    // Ajoute un site
    public function store(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'response' => 'required|string|max:255',
            'port' => 'required|string|max:255',
            'method' => 'required|string|max:255',
            'header' => 'required',
        ]);

        Site::create([
            'url' => $request->url,
            'name' => $request->name,
            'type' => $request->type,
            'response' => $request->response,
            'port' => $request->port,
            'method' => $request->method,
            'header' => $request->header,
        ]);

        return redirect()->back()->with('success', 'Site ajouté avec succès !');
    }

    // Vérifie le statut des sites
    public function checkSite(Site $site)
    {
        try {
            $response = Http::get($site->url);
            $status = $response->successful();
            $message = $status ? 'Le site est en ligne' : 'Le site est hors ligne';
        } catch (\Exception $e) {
            $status = false;
            $message = 'Erreur de connexion : ' . $e->getMessage();
        }

        // Enregistrer le statut
        $site->statuses()->create([
            'status' => $status,
            'message' => $message,
        ]);

        // Vérifier si le statut a changé
        if ($site->status != $status) {
            // Envoyer une alerte via ntfy.sh
            Http::post('https://ntfy.sh/jobAlert', [
                'topic' => 'Alerte de statut de site',
                'message' => $message . ' - ' . $site->url,
                'title' => 'Changement de statut de site',
            ]);

            // Mettre à jour le statut du site
            $site->update(['status' => $status]);
        }
    }
  
    // Affiche l'historique des vérifications pour un site
    public function show(Site $site)
    {
        $statuses = $site->statuses()->orderBy('created_at', 'desc')->get();

    // Définir la date limite (90 jours en arrière)
    $ninetyDaysAgo = Carbon::now()->subDays(90);

    // Récupérer le nombre de dates distinctes pour les statuts dans les 90 derniers jours
    $distinct_dates_count = $site->statuses()
    ->where('created_at', '>=', $ninetyDaysAgo)  // Filtrer les 90 derniers jours
    ->selectRaw('DATE(created_at) as date')  // Sélectionner uniquement la date sans l'heure
    ->groupBy('date')  // Grouper par jour
    ->get()
    ->count(); 

        return view('sites.show',['navbar_active'=>'analyse'], compact('site', 'statuses','distinct_dates_count'));
    }
    public function captureScreenshot(Site $site)
{
    // Chemin du script Node.js
    $scriptPath = base_path('scripts/screenshot.js');

    // Générer un nom de fichier unique pour la capture d'écran
    $fileName = 'screenshot_' . $site->id . '_' . time() . '.png';

    // Chemin pour enregistrer l'image dans le répertoire 'public/screenshots'
    $outputPath = public_path('screenshots/' . $fileName);

    // Exécuter le script Node.js avec les paramètres (URL du site et chemin de sortie)
    $command = "/opt/alt/alt-nodejs20/root/usr/bin/node $scriptPath {$site->url} $outputPath";
    exec($command, $output, $return_var);

    if ($return_var === 0) {
        // Sauvegarder le chemin de la capture dans la base de données si nécessaire
        $site->screenshot_path = 'screenshots/' . $fileName;
        $site->save();

        return redirect()->back()->with('success', 'Capture d\'écran réalisée avec succès.');
    } else {
        return redirect()->back()->with('error', 'Erreur lors de la capture d\'écran.');
    }
}

function SendEmail($to, $subject, $content) {

    $use_smtp = true;

    $mail = new PHPmailer;
    
    if($use_smtp){
    
      $mail->IsSMTP();
      $mail->SMTPDebug = 2; // afficher dans les logs les commandes client et les réponses du serveur
      $mail->Debugoutput = 'error_log'; // dans les logs
      $mail->url='gabriel0.com';
      $mail->Port = 465;
      $mail->Username = 'status@gabriel0.com';
      $mail->Password = env('SMTP_PASSWORD');
      $mail->SMTPAuth = true;
      $mail->SMTPSecure = 'ssl';
    
    }
    
   
    
    $mail->AddReplyTo('status@gabriel0.com');
    $mail->SetFrom('status@gabriel0.com', ('JobAlert'));
    $mail->AddAddress('gabriel.schallenberger@outlook.com');
    $mail->CharSet = "UTF-8";
    $mail->Subject = $subject;
    $mail->Body    = $content;
    $mail->AltBody = ('go check something is wrong');
    $mail->AddCustomHeader("List-Unsubscribe: <mailto:status@gabriel0.com?subject=Unsubscribe>, <https://gabriel0.com>");
    
    if(!$mail->send()) {
      echo 'Erreur: ' . $mail->ErrorInfo;
    } 
    else {
      $mail_envoye = true;
    }
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'ntfy.sh/jobAlert');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $subject);

    $headers = array();
    $headers[] = 'Content-Type: application/x-www-form-urlencoded';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);
    unset($mail);
}

function IcmpPing($url) {
    $package = hex2bin("080000005243430001");
    for($i = strlen($package); $i < 64; $i++) {
        $package .= chr(0);
    }
    $tmp = unpack("n*", $package);
    $sum = array_sum($tmp);
    $sum = ($sum >> 16) + ($sum & 0xFFFF);
    $sum = $sum + ($sum >> 16);
    $sum = ~ $sum;
    $checksum   = pack("n*", $sum);
    $package[2] = $checksum[0];
    $package[3] = $checksum[1];
    $socket     = socket_create(AF_INET, SOCK_RAW, getprotobyname('icmp'));
    $start      = microtime(true);
    socket_sendto($socket, $package, strlen($package), 0, $url, 0);
    $read   = array($socket);
    $write  = null;
    $except = null;
    $select = socket_select($read, $write, $except, 5);
    $error  = null;
    if ($select === false) {
        $error = 'Failed to create socket: ' . socket_strerror(socket_last_error());
        socket_close($socket);
    } else if($select === 0) {
        $error = "Request timeout";
        socket_close($socket);
    }
    if($error !== null) {
        return $error;
    }
    socket_recvfrom($socket, $recv, 65535, 0, $url, $port);
    $end      = microtime(true);
    $recv     = unpack("C*", $recv);
    $length   = count($recv) - 20;
    $ttl      = $recv[9];
    $seq      = $recv[28];
    $duration = round(($end - $start) * 1000,3);
    socket_close($socket);
    return [
        'length' => $length,
        'url'   => $url,
        'seq'    => $seq,
        'ttl'    => $ttl,
        'time'   => $duration,
    ];
}

public function CheckHttpService($url, $status, $response) {
    $errLvl = error_reporting(0);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION,true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 3);
    curl_setopt($curl, CURLOPT_COOKIEJAR, 'cookies.txt');
    curl_setopt($curl, CURLOPT_COOKIEFILE, 'cookies.txt');
    $headers = array();
    $headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7';
    $headers[] = 'Accept-Language: fr-FR,fr;q=0.9,en-US;q=0.8,en;q=0.7';
    $headers[] = 'Cache-Control: no-cache';
    $headers[] = 'Connection: keep-alive';
    $headers[] = 'Pragma: no-cache';
    $headers[] = 'Sec-Fetch-Dest: document';
    $headers[] = 'Sec-Fetch-Mode: navigate';
    $headers[] = 'Sec-Fetch-Site: same-site';
    $headers[] = 'Sec-Fetch-User: ?1';
    $headers[] = 'Upgrade-Insecure-Requests: 1';
    $headers[] = 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36';
    $headers[] = 'Sec-Ch-Ua: \"Google Chrome\";v=\"123\", \"Not:A-Brand\";v=\"8\", \"Chromium\";v=\"123\"';
    $headers[] = 'Sec-Ch-Ua-Mobile: ?0';
    $headers[] = 'Sec-Ch-Ua-Platform: \"macOS\"';
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    
    $result = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    error_reporting($errLvl);
    if($status && $httpCode != $status) {
        return ["success" => false, 'http_status'=>$httpCode,"reason" => "HTTP status code error: {$httpCode}, expected {$status}"];
    }
    if(!empty($response) && strpos($result, $response) === false) {
        return ["success" => false, 'http_status'=>$httpCode,"reason" => "HTTP response error: {$response}, expected {$response}"];
    }
    return ['http_status'=>$httpCode,"success" => true];
}

function CheckTcpService($url, $port) {
    $fp = @fsockopen($url, $port, $errno, $errstr, _E('TIMEOUT_SEC'));
    if(!$fp) {
        return ["success" => false, "reason" => $errstr];
    }
    fclose($fp);
    return ["success" => true];
}

function CheckUdpService($url, $port) {
    $fp = @fsockopen("udp://{$url}", $port, $errno, $errstr, _E('TIMEOUT_SEC'));
    if(!$fp) {
        return ["success" => false, "reason" => $errstr];
    }
    fclose($fp);
    return ["success" => true];
}

function CheckIcmpService($url) {
    $result = IcmpPing($url);
    if (is_string($result)) {
        return ["success" => false, "reason" => $result];
    }
    return ["success" => true];
}

function PrintLog() {
    $args = func_get_args();
    $str = implode(' ', $args);
    echo sprintf('[%s] %s', date('Y-m-d H:i:s'), $str) . PHP_EOL;
}

function GetSiteConfig() {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM `config`');
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $config = [];
    foreach($result as $row) {
        $config[$row['key']] = $row['value'];
    }
    return $config;
}

function GetUserByUsername($username) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM `users` WHERE `username` = ?');
    $stmt->execute([$username]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
}

function GetUserById($id) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM `users` WHERE `id` = ?');
    $stmt->execute([$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
}

function GetUserByEmail($email) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM `users` WHERE `email` = ?');
    $stmt->execute([$email]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
}

function GetServices() {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM `services`');
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $services = [];
    foreach($result as $row) {
        $row['extra'] = json_decode($row['extra'], true) ?: [];
        $services[] = $row;
    }
    return $services;
}
public function update(Request $request, Site $site)
{
    // Validation des données
    $request->validate([
        'url' => 'required|url',
        'name' => 'required|string|max:255',
        'status' => 'nullable|string',
        'screenshot_path' => 'nullable|string',
        'response' => 'nullable|string',
        'type' => 'nullable|string',
        'port' => 'nullable|integer',
        'header' => 'nullable|string',
        'method' => 'required|in:GET,POST,PUT,DELETE'
    ]);

    // Mise à jour du site
    $site->update($request->all());

    // Rediriger avec un message de succès
    return redirect()->route('sites.show', $site->id)
                     ->with('success', 'Le site a été mis à jour avec succès.');
}


public function CheckServices($service) {
 
        switch(strtolower($service['type'])) {
            case 'http':
                $url      = $service['url'];
                $status   = $service['status'] ?: Intval($service['status']);
                $response = $service['response'];
                $result   = $this->CheckHttpService($url, $status, $response);
                break;
            case 'tcp':
                $url   = $service['url'];
                $port   = Intval($service['port']);
                $result = $this->CheckTcpService($url, $port);
                break;
            case 'udp':
                $url   = $service['url'];
                $port   = Intval($service['port']);
                $result = $this->CheckUdpService($url, $port);
                break;
            case 'icmp':
                $url   = $service['url'];
                $result = $this->CheckIcmpService($url);
                break;
            default:
                $result = false;
                break;
        }
        if($result['success']){
            $service->statuses()->create([
                'http_status'=>$result['success'],
                'status' => $result['success'] ? TRUE : FALSE,
                'message' => 'Le site est en ligne',
            ]);
        }else{
            $service->statuses()->create([
                'http_status'=>$result['success'],
                'status' => $result['success'] ? TRUE : FALSE,
                'message' => $result['reason'],
            ]);
        }
       
        
}

function UpdateServiceStatus($id, $status, $reason = null) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM `status` WHERE `service` = ? AND `date` = ?');
    $stmt->execute([$id, date('Y.m.d')]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result === false) {
        $stmt = $pdo->prepare('INSERT INTO `status` (`service`, `date`, `status`, `incident`) VALUES (?, ?, ?, ?)');
        $stmt->execute([$id, date('Y.m.d'), $status, $reason]);
    } else {
        // if ($result['status'] == 'error'){
        //     SendNotification($id, $status, $reason);
        // }
        // if ($result['status'] == 'normal' || $status == 'normal' || ($result['status'] == 'warning' && $status == 'error')) {
        //     if ($reason) {
        //         $stmt = $pdo->prepare('UPDATE `status` SET `status` = ?, `incident` = ? WHERE `id` = ?');
        //         $stmt->execute([$status, $reason, $result['id']]);
        //     } else {
        //         $stmt = $pdo->prepare('UPDATE `status` SET `status` = ? WHERE `id` = ?');
        //         $stmt->execute([$status, $result['id']]);
        //     }
        //     if ($status !== $result['status']) {
        //         SendNotification($id, $status, $reason);
        //     }
            
         
        // }
        // 服务状态发生变化时，更新状态和异常记录
        if ($result['status'] !== $status) {
            $incidents = json_decode($result['incident'], true) ?: [];
            // 判断有无异常的 end 为空，如果有则更新 end 为当前时间
            foreach($incidents as $key => $incident) {
                if ($incident['end'] === null) {
                    $incidents[$key]['end'] = time();
                    break;
                }
            }
            // 新增一条异常记录
            $incidents[] = [
                'start'  => time(),
                'end'    => null,
                'status' => $status,
                'reason' => $reason
            ];
            $stmt = $pdo->prepare('UPDATE `status` SET `status` = ?, `incident` = ? WHERE `id` = ?');
            $stmt->execute([$status, json_encode($incidents), $result['id']]);
            SendNotification($id, $status, $reason);
        }
    }
}

function SendNotification($id, $status, $reason = null) {
	echo "hey";
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM `services` WHERE `id` = ?');
    $stmt->execute([$id]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($service === false) {
        return;
    }
    $stmt = $pdo->prepare('SELECT * FROM `users`');
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($users as $user) {
        if ($user['email'] && _E('NOTIFY_EMAIL')) {
            echo "hoo";
            SendEmail($user['email'], _UF('notify.email.title', $service['name']), GetMailTemplate($service['name'], $status, $reason));
        }
    }
    $statusText = _U("status.label.{$status}");
   
}

function GetMailTemplate($name, $status, $reason) {
    $statusText = _U("status.label.{$status}");
    $reason = $reason ?? _U('notify.reason.none');
    return _UF('notify.email.content', $name, $statusText, $reason);
}



}
