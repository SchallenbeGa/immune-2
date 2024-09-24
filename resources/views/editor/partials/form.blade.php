<div class="post-page">
<form method="post"

@if (isset($article))
  hx-post="/htmx/editor/{{ $article->slug }}"
@else
  hx-post="/htmx/editor"
@endif

hx-target="#app-body"
>

<div id="form-message"></div>

  <div class="banner">
    <div class="container">

   <h1> <fieldset class="form-group">
            <input type="text" name="title" class="form-control form-control-lg" placeholder="Post Title"
              @if (isset($article))
                value="{{ $article->title }}"
              @endif
            >
          </fieldset></h1>
          
          <hr>
          <fieldset class="form-group">
            <input type="text" name="description" class="form-control form-control-md" placeholder="What's this article about?"
              @if (isset($article))
                value="{{ $article->description }}"
              @endif
            >
          </fieldset>

      <div class="post-meta">
       
        <div class="info">
          <span class="date">{{ $article->created_at->format('F jS') }}</span>
        </div>

      </div>

    </div>
  </div>

  <div class="container page">

    <div class="row post-content">
      <div class="col-md-12">
      <fieldset class="form-group">
            <textarea rows="16" name="content" class="form-control" placeholder="Write your post (in markdown)">@if (isset($article)){!! $article->body !!}@endif</textarea>
          </fieldset>
          
      
      </div>
      
    </div>
    <fieldset class="form-group">
            <input type="text" name="tags" class="form-control tagify--outside" placeholder="Enter tags"
              @if (isset($article))
                value="{{ $article->tags->pluck('name') }}"
              @endif
            >
          </fieldset>
          <button  class="btn btn-lg btn-primary pull-xs-right">
            Publish Article
          </button>
          
  </div>
  </form>
</div>
<script src="{{ asset('js/tagify.js') }}"></script>

<script>
  var isTagify = null;

  document.body.addEventListener('htmx:configRequest', function(evt) {
    evt.detail.headers['X-CSRF-TOKEN'] = '{{ csrf_token() }}';
  })

  window.addEventListener('DOMContentLoaded', function() {
    renderTagify();
  });

  document.body.addEventListener("htmx:afterSwap", function(evt) {
    renderTagify();
  });

  function renderTagify() {
    const input = document.querySelector('input[name=tags]');
    const tagify = document.querySelector('tags[class="tagify  form-control tagify--outside"]');

    if (input && !tagify) {
      new Tagify(input, {
        whitelist: [],
        dropdown: {
          position: "input",
          enabled : 0 // always opens dropdown when input gets focus
        }
      })
    }
  }
</script>