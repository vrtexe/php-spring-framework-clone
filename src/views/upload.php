<html>

<head>

</head>

<style>
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
        text-decoration: none;
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
        <li><a href="/upload" class="active">Upload</a></li>
        <li><a href="/index">Files</a></li>
    </ul>
</nav>

<h1>Upload</h1>

<form method="post" action="/upload" enctype="multipart/form-data">
    <label for="data">Data</label>
    <input type="file" name="data" id="data">
    <button type="submit">upload</button>
</form>

</body>

