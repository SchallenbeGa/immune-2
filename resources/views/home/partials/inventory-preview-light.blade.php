<div id="inventory-preview-light" hx-swap-oob="true">
<h3>list</h3>
  @forelse ($computers as $computer)
    <div class="post-preview">
      <div class="post-meta">
       

        <div class="info">
         
          <span class="date">{{ $computer->created_at->format('F jS') }}</span>
        </div>

        
      </div>
      <a href="/computers/{{ $computer->reference }}"
        hx-push-url="/computers/{{ $computer->reference }}"
        hx-get="/htmx/computers/{{ $computer->reference }}"
        hx-target="#app-body"
        class="preview-link"
      >
        <p>{{ $computer->employee }}</p>

      
      </a>
    </div>

   
  @empty
  <div class="post-preview">
    <div class="alert alert-warning" role="alert">
      No computers are here... yet.
    </div>
  </div>
  @endforelse

  <table>
        <thead>
            <tr>
                <th>Référence de la machine</th>
                <th>Nom de l'utilisateur</th>
                <th>Date mise à jour</th>
                <th>Date d'ajout</th>
            </tr>
        </thead>
        <tbody>
            @foreach($computers as $computer)
                <tr>
                    <td>{{ $computer->reference }}</td>
                    <td>{{ $computer->employee->name }}</td>
                    <td>{{ $computer->updated_at->format('d.m.Y') }}</td>
                    <td>{{ $computer->created_at->format('d.m.Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>