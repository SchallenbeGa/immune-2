<div class="row mb-2" id="article-post-preview" hx-swap-oob="true">
    @forelse ($articles as $entry)
    <div class="col-md-6">
        <div class="card flex-md-row mb-4 box-shadow h-md-250">
            <div class="card-body d-flex flex-column align-items-start">
                <strong class="d-inline-block mb-2 text-primary">AI Generated</strong>
                <h3 class="mb-0">
                <a href="/articles/{{ $entry->slug }}" style="text-decoration: none;color: var(--bs-heading-color);" hx-push-url="/articles/{{ $entry->slug }}" hx-get="/htmx/articles/{{ $entry->slug }}" hx-target="#app-body" class="preview-link">{{ $entry->title }}</a>
                </h3>
                <div class="mb-1 text-muted">{{$entry->created_at->format('F jS')}}</div>
                <p class="card-text mb-auto">{{$entry->description}}</p>
                <a href="/articles/{{ $entry->slug }}" hx-push-url="/articles/{{ $entry->slug }}" hx-get="/htmx/articles/{{ $entry->slug }}" hx-target="#app-body" class="preview-link">Continue reading</a>
            </div>
            <img class="card-img-right flex-auto d-none d-md-block"  alt="Thumbnail [200x250]" style="width: 200px;" src="{{ $entry->img_path }}">
        </div>
    </div>
@empty
<div class="post-preview">
    <div class="alert alert-warning" role="alert">
        Nothing to see here...
    </div>
</div>
@endforelse