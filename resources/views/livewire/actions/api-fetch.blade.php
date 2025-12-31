<div>

</div>
@script
    <script>
        Livewire.on('api-fetch-run-{{ $id }}',
            function(response) {
                console.log(response);
                const url = response[0].url
                const data = response[0].data;
                const token = response[0].token;

                async function postData() {
                    const response = await fetch(url, {
                        method: 'POST',
                        mode: 'cors',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                        },
                        body: JSON.stringify(data),
                    });

                    if (!response.ok) {
                        throw new Error(`Error: ${response.status} ${response.statusText}`);
                    }

                    $wire.dispatch('api-fetch-update-response-{{ $id }}', {
                        response: response
                    });

                    return response.json();
                }

                postData();
            })
    </script>
@endscript
