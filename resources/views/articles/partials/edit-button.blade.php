<a style="margin:0.2rem;" style="color:black" class="btn btn-sm edit-button"
  href="/editor/{{ $article->slug }}"
  hx-target="#app-body"
  hx-push-url="/editor/{{ $article->slug }}"
>
  <i class="ion-edit"></i>
  Edit Article
</a>