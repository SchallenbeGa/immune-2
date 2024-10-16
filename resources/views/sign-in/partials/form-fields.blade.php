@csrf
<fieldset class="form-group">
  <input type="text" id="sign-in-email" class="form-control form-control-lg" name="email" placeholder="Email" value="{{ (isset($oldEmail) ? $oldEmail : old('email')) }}">
</fieldset>
<fieldset class="form-group">
  <input type="password" id="sign-in-password" class="form-control form-control-lg" name="password" placeholder="Password">
</fieldset>
<button class="btn btn-lg btn-primary pull-xs-right" id="login_btn">
  Sign in
</button>
</button>
<button class="btn btn-lg btn-primary pull-xs-right" id="demo_login" onclick="autologin()">
  Demo
</button>
<script>
document.getElementById('demo_login').addEventListener('click', 
    function(event) {
        event.preventDefault();
        document.getElementById("sign-in-email").value = 'test@email.com';
document.getElementById("sign-in-password").value = 'Colombier$2022';
document.getElementById("login_btn").click();
});


</script>