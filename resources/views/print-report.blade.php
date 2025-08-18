@use('App\Enums\TransactionType', 'TransactionType')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{!! $report->name !!}</title>
    <style>
        @page {
            margin: 1.5cm;
        }
    
        body {
            font-family: sans-serif;
            font-size: 14px;
            margin: 0;
        }
    
        h1, h2, p {
            margin: 0 0 0.5rem 0;
        }
    
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
    
        thead {
            background: #eee;
        }
    
        table, th, td {
            border: 1px solid #ccc;
            padding: 0.5rem;
            text-align: left;
        }
    
        /* Repeat headers on every page */
        thead { display: table-header-group; }
        tfoot { display: table-footer-group; }
    
        /* Prevent row splitting */
        tr {
            page-break-inside: avoid;
            break-inside: avoid;
        }
    
        /* Ensure table starts with some spacing on new pages */
        tbody tr:first-child {
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <h1>{!! $report->name !!}</h1>

    @if ($report->account)
        <p><strong>Account:</strong> {{ $report->account->name }}</p>
    @endif

    @if ($report->type)
        <p><strong>Transaction Type:</strong> {{ $report->type->label() }}</p>
    @endif

    @if ($report->category)
        <p><strong>Category:</strong> {{ $report->category->name }}</p>
    @endif

    @if ($report->tag)
        <p><strong>Tag:</strong> {{ $report->tag->name }}</p>
    @endif

    @if ($report->payees)
        <p><strong>Payees:</strong> {!! implode(', ', $report->payees) !!}</p>
    @endif

    <p>
        <strong>Date:</strong>

        {{ $report->start_date->format('M j, Y') }} – {{ $report->end_date->format('M j, Y') }}
    </p>

    <h2>Transactions</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Payee</th>
                <th>Account</th>
                <th>Category</th>
                <th>Type</th>
                <th style="text-align: right;">Amount</th>
            </tr>
        </thead>
        
        <tbody>
            @foreach ($report_transactions as $transaction)
                <tr>
                    <td>{{ $transaction->date->format('M j, Y') }}</td>
                    <td>{!! $transaction->payee !!}</td>
                    <td>{{ $transaction->snapshot['account']['name'] ?? '' }}</td>
                    <td>{{ $transaction->snapshot['category']['name'] ?? '' }}</td>
                    <td>{{ $transaction->type->label() }}</td>
                    <td style="text-align: right;">
                        @if (in_array($transaction->type, [
                                TransactionType::DEBIT,
                                TransactionType::TRANSFER,
                                TransactionType::WITHDRAWAL
                            ])
                        )
                            <span>-</span>
                        @else
                            <span>+</span>
                        @endif
                        ${{ Number::format($transaction->amount ?? 0, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <script>
        window.addEventListener('load', () => window.print());
    </script>
</body>
</html>
