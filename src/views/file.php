<html lang="en">

<head>

    <title>Transactions</title>
    <?php
    /** @var TransactionsFileDto $model */

    use Vangel\Project\Model\TransactionsFileDto;

    ?>
</head>

<style>
    table {
        width: 100%;
        border-collapse: collapse;
        text-align: center;
    }

    th,
    td {
        padding: 5px;
        border: 1px #eee solid;
    }

    tfoot td,
    tfoot th {
        font-size: 20px;
    }

    tfoot th {
        text-align: right;
    }

    td[data-value] {
        color: green;
    }

    td[data-value^="-$"] {
        color: red;
    }

    nav ul {
        display: flex;
        gap: 1rem;
        background-color: #eeeeee;
        list-style: none;
    }

    nav ul li {
        padding: 0.5rem;
    }

    nav ul li a {
        text-decoration:  none;
        color: slategray;
    }

    nav ul li:has(a.active) {
        border-bottom: 1px solid #bf7832;
    }

    nav ul li a.active {
        color: #bf7832;
    }
</style>

<body>

<nav>
    <ul>
        <li><a href="/upload">Upload</a></li>
        <li><a href="/index">Files</a></li>
    </ul>
</nav>

<h1>
    <?= $model->file ?>

</h1>


<table>
    <thead>
    <tr>
        <?php foreach ($model->headers as $header) : ?>
            <th>
                <?= $header ?>
            </th>
        <?php endforeach; ?>
    </tr>
    </thead>

    <tbody>
    <?php foreach ($model->transactions as $transaction) : ?>
        <tr>
            <td><?= $transaction->formattedDate() ?></td>
            <td><?= $transaction->checkId ?> </td>
            <td><?= $transaction->description ?></td>
            <td data-value="<?= $transaction->formattedAmount() ?>"><?= $transaction->formattedAmount() ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>

    <tfoot>
    <tr>
        <th colspan="3">Total Income:</th>
        <td><?= $model->getTotalIncomeFormatted() ?></td>
    </tr>
    <tr>
        <th colspan="3">Total Expense:</th>
        <td><?= $model->getTotalExpensesFormatted() ?></td>
    </tr>
    <tr>
        <th colspan="3">Net Total:</th>
        <td><?= $model->getTotalFormatted() ?></td>
    </tr>
    </tfoot>
</table>
</body>

