
<div class="col-md-12 col-xs-12" style="margin-bottom:1rem;">
<form
  action="/import"
  method="POST"
  hx-post="/htmx/import"
  id="import-form"
  enctype="multipart/form-data"
  @if (isset($oob_swap) && $oob_swap)
    hx-swap-oob="true"
  @endif
>
    @csrf
    <label for="json_file">import json :</label>
    <input type="file" name="json_file" id="json_file">
    <button class="btn btn-lg btn-primary" type="submit" onclick="setTimeout(() =>refreshTableData(), 1000)">letsgooo</button>
</form>
<form
  action="/import/csv"
  method="POST"
  hx-post="/htmx/import/csv"
  id="import-form"
  enctype="multipart/form-data"
  @if (isset($oob_swap) && $oob_swap)
    hx-swap-oob="true"
  @endif
>
    @csrf
    <label for="csv_file">import csv :</label>
    <input type="file" name="csv_file" id="csv_file">
    <button class="btn btn-lg btn-primary" type="submit" onclick="setTimeout(() =>refreshTableData(), 1000)">letsgooo</button>
</form>
</div>