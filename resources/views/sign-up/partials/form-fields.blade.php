@csrf
<fieldset class="form-group">
  <input id="sign-up-username" style="margin-top:10px" class="form-control form-control-lg" type="text" name="username" placeholder="Username" value="{{ (isset($oldUsername) ? $oldUsername : old('username')) }}">
</fieldset>
<fieldset class="form-group">
  <input id="sign-up-email" style="margin-top:10px" class="form-control form-control-lg" type="text" name="email" placeholder="Email" value="{{ (isset($oldEmail) ? $oldEmail : old('email')) }}">
</fieldset>
<fieldset class="form-group">
  <input id="sign-up-password" style="margin-top:10px" class="form-control form-control-lg" type="password" name="password" placeholder="Password">
</fieldset>
<button class="btn btn-lg btn-primary pull-xs-right" style="margin-top:10px">
  Sign up
</button>