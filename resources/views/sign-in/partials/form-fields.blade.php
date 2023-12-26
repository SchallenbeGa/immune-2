@csrf
<fieldset class="form-group">
  <input type="text" id="sign-in-email" style="margin-top:10px" class="form-control form-control-lg" name="email" placeholder="Email" value="{{ (isset($oldEmail) ? $oldEmail : old('email')) }}">
</fieldset>
<fieldset class="form-group">
  <input type="password" id="sign-in-password" style="margin-top:10px" class="form-control form-control-lg" name="password" placeholder="Password">
</fieldset>
<button class="btn btn-lg btn-primary pull-xs-right" style="margin-top:10px">
  Sign in
</button>