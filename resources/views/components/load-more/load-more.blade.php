 @props([
     'items' => $items,
 ])


 @if ($items->hasMorePages())
     <div x-data="{
         loading: false,
         observe() {
             let observer = new IntersectionObserver((entries) => {
                 entries.forEach(entry => {
                     if (entry.isIntersecting) {
                         this.loading = true;
                         @this.call('loadMore')
                             .finally(() => {
                                 this.loading = false;
                             });
                     }
                 })
             }, {
                 root: null
             })
     
             observer.observe(this.$el)
         }
     }" x-init="observe">

         <x-load-more.spinner />

     </div>
 @endif
