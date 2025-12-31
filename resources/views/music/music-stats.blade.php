<x-layouts.app>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <section class="p-4">
        <header class="mb-6">
            <h1 class="text-3xl text-primary">Music stats</h1>
        </header>



        <div class="table-layout">
            <table>
                <thead>
                    <th>Format</th>
                    <th>Amount</th>
                </thead>

                @foreach ($albumFormatsOwned as $index => $albumFormatOwned)
                    <tr>
                        <td>
                            {{ $albumFormatOwned['name'] }}
                        </td>
                        <td>
                            {{ $albumFormatOwned['format_count'] }}
                        </td>
                    </tr>
                @endforeach
            </table>
            <br />
            <table>
                <thead>
                    <th>Year</th>
                    <th>Owned<br /><span class="text-sm">(all formats)</span></th>
                    <th>Owned<br /><span class="text-sm">(unique formats)</span></th>
                </thead>
                @foreach ($albumAmountPerYear as $index => $item)
                    <tr>
                        <td>
                            {{ $index }}
                        </td>
                        <td>
                            {{ $item['amount_all'] }}
                        </td>
                        <td>
                            {{ $item['amount_unique'] }}
                        </td>
                    </tr>
                @endforeach
            </table>


            <canvas id="barChart"></canvas>

            <script>
                const ctx = document.getElementById('barChart').getContext('2d');

                // Data from Laravel
                const data = @json($albumAmountPerYear);

                const labels = data.map(item => item.year);
                const amounts = data.map(item => item.amount);

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Amount',
                            data: amounts,
                            backgroundColor: '',
                            borderColor: 'rgb(217 119 6 )',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        indexAxis: 'y', // Horizontal bar
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    color: '#aaa', // Change x-axis label color
                                    font: {
                                        size: 16 // Increase legend font size
                                    }
                                }
                            },
                            y: {
                                ticks: {
                                    color: '#aaa', // Change y-axis label color
                                    font: {
                                        size: 12 // Increase legend font size
                                    },
                                    maxTicksLimit: labels.length, // Ensure all labels are displayed
                                    autoSkip: false // Do not skip labels
                                },

                            }
                        },

                        plugins: {
                            legend: {
                                labels: {
                                    color: '#aaa', // Change legend text color
                                }
                            }
                        },

                    },
                });
            </script>
    </section>
</x-layouts.app>
