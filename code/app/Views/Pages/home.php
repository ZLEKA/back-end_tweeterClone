
<!doctype html>
<html lang="en" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Twitter clone">
    <meta name="author" content="You, of course">
    <!-- Bootstrap core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <title>Twitter 0.5</title>

</head>
<body>

<nav class="navbar navbar-expand-md navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Twitter 0.5</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav me-auto mb-2 mb-md-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#">Home</a>
                </li>
            </ul>
            <form class="d-flex">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
            </form>
        </div>
    </div>
</nav>

<main class="container" style="width: 50%;">
    <?php foreach($tweets as $tweet): ?>
        <div class="card">
            <div class="card-header">
                <?= $tweet->id ?>
                <?= $tweet->created_at ?>
            </div>
            <div class="card-body">
                <blockquote class="blockquote mb-0">
                    <p class="card-text"><?= $tweet->content ?></p>
                    <h5 class="card-title">Comments</h5>
                </blockquote>
                <br>
                <?php foreach($tweet->comments() as $comment): ?>
                    <div class="card">
                        <div class="card-header">
                            <?= $comment->created_at ?>
                        </div>
                        <div class="card-body">
                            <blockquote class="blockquote mb-0">
                                <p class="card-text"><?= $comment->content ?></p>
                            </blockquote>
                        </div>
                    </div>
                    <hr>
                <?php endforeach; ?>
            </div>
        </div>
        <hr>
    <?php endforeach; ?>
</main>

</body>
</html>
