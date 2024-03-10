<div id="feed-post-preview" hx-swap-oob="true">
  <p>{{$total_invested}}$ invested from 2024-03-04 08:00:00 <br> Generated : ~<span style="color:green">{{ $total }}$</span> / fee rate 0.1000%</p>
  <hr style="border: 10px solid;border-radius: 5px;">
  <h4 class="fst-italic">Order pending</h4>
  <div class="row">
     
 
  @forelse ($orders as $entry)
  <div class="col-6">
  <div class="p-4 mb-3 bg-body-tertiary rounded" style=" border-radius: 15px 50px 30px !important;
  background: #73AD21;
  padding: 20px;
  height: 100px;">
          
          <p class="mb-0">
            <span class="date">{{ $entry->created_at }}</span>
            <br>
            <span class="order_content">{{ $entry->side }} at price : {{ $entry->price }}</span></p>
        </div>
  </div>
  @empty
  <div class="post-preview">
    <div class="alert alert-warning" role="alert">
      No articles are here... yet.
    </div>
  </div>
  @endforelse
  @forelse ($symbol as $entry)
  
    <article class="blog-post">
    <div class="row">
      <div class="col">
      <h2 class="display-5 link-body-emphasis mb-1" style="  display: flex;flex-flow: row wrap;align-items: center;">
        <a href="/symbol/{{ $entry->name }}" style="text-decoration: none;color: var(--bs-heading-color);" hx-push-url="/symbol/{{ $entry->name }}" hx-get="/htmx/symbol/{{ $entry->name }}" hx-target="#app-body" class="preview-link">{{ $entry->name }}</a>
      </h2>
      </div>
      <div class="col">
      @include('home.partials.symbol-favorite-button', [
      'symbol' => $entry,
      'favorite_count' => $entry->favoritedUsers->count(),
      'is_favorited' => auth()->user() ? $entry->favoritedByUser(auth()->user()) : false
      ])
      </div>
    </div>
        <p class="blog-post-meta">{{ $entry->updated_at }}</p>
        <p>1k $ invested {{ $entry->created_at->format('F jS') }} <br> Generated : <span style="color:green">{{$entry->profit}}</span> ( {{$entry->nb_trade ?? 0}} trades)</p>
        <hr>
      </a>
     
    </article>
 
@empty
<div class="post-preview">
  <div class="alert alert-warning" role="alert">
    Nothing to see here...
  </div>
</div>
@endforelse
</div>