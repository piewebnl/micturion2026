 @if (session('info') || session('success') || session('error') || session('warning'))

     <div role="status" class="pointer-events-none z-50 flex w-full flex-col">

         @if (session('info'))
             <div class="pointer-events-auto relative mb-4 w-full max-w-xs">
                 <i class="bg-info text-info info inline-block w-full select-none rounded px-6 py-3 not-italic">
                     {{ session('info') }}
                 </i>
             </div>
         @endif

         @if (session('success'))
             <div class="pointer-events-auto relative mb-4 w-full max-w-xs">
                 <i class="bg-success text-success inline-block w-full select-none rounded px-6 py-3 not-italic">
                     {{ session('success') }}
                 </i>
             </div>
         @endif

         @if (session('error'))
             <div class="pointer-events-auto relative mb-4 w-full max-w-xs">
                 <i class="bg-error text-error inline-block w-full select-none rounded px-6 py-3 not-italic">
                     {{ session('error') }}
                 </i>
             </div>
         @endif

         @if (session('warning'))
             <div class="pointer-events-auto relative mb-4 w-full max-w-xs">
                 <i class="bg-warning text-warning inline-block w-full select-none rounded px-6 py-3 not-italic">
                     {{ session('warning') }}
                 </i>
             </div>
         @endif

     </div>

 @endif
