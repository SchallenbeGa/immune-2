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
    <button type="submit" onclick="setTimeout(() =>refreshTableData(), 1000)">letsgooo</button>
</form>