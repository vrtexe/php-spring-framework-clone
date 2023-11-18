<html lang="en">

<head>
    <title>Transactions</title>
    <?php

    /** @var FilesDto $model */

    use Vangel\Project\Model\FilesDto;

    ?>
</head>

<style>
    li {
        display: flex;
        gap: 0.5rem;
    }

    button {
        border: black 1px solid;
        border-radius: 5px;
        padding: 0.25rem 0.5rem;
        background-color: #ed4e76;
        color: white;
        cursor: pointer;
    }

    button:hover {
        background-color: #faacc0;
        color: black;
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
        <li><a href="/index" class="active">Files</a></li>
    </ul>
</nav>

<h1>Files</h1>

<ul>
    <?php foreach ($model->files as $file) : ?>
        <li>
            <a href="/file?q=<?= $file ?>">
                <?= $file ?>
            </a>

            <form action="/delete" method="get">
                <input type="hidden" name="q" value="<?= $file ?>">
                <button type="submit">Delete</button>
            </form>

        </li>
    <?php endforeach; ?>
</ul>
</body>