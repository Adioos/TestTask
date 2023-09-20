<?php
//initialize facebook sdk
require 'vendor/autoload.php';
session_start();
$fb = new Facebook\Facebook([
    'app_id' => '1555126128353316', // your app id
    'app_secret' => 'f95debbaf3bc7a31fd7ea098f0072fb5', // your app secret
    'default_graph_version' => 'v2.5',
]);
$helper = $fb->getRedirectLoginHelper();
$permissions = ['email']; // optional
try {
    if (isset($_SESSION['facebook_access_token'])) {
        $accessToken = $_SESSION['facebook_access_token'];
    } else {
        $accessToken = $helper->getAccessToken();
    }
} catch (Facebook\Exceptions\facebookResponseException $e) {
    // When Graph returns an error
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
} catch (Facebook\Exceptions\FacebookSDKException $e) {
    // When validation fails or other local issues
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
}
if (isset($accessToken)) {
    if (isset($_SESSION['facebook_access_token'])) {
        $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
    } else {
        // getting short-lived access token
        $_SESSION['facebook_access_token'] = (string) $accessToken;
        // OAuth 2.0 client handler
        $oAuth2Client = $fb->getOAuth2Client();
        // Exchanges a short-lived access token for a long-lived one
        $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);
        $_SESSION['facebook_access_token'] = (string) $longLivedAccessToken;
        // setting default access token to be used in script
        $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
    }

    // getting basic info about user
    try {
        $profile_request = $fb->get('/me?fields=name,first_name,last_name,email');
        $requestPicture = $fb->get('/me/picture?redirect=false&height=200'); //getting user picture
        $picture = $requestPicture->getGraphUser();
        $profile = $profile_request->getGraphUser();
        $fbid = $profile->getProperty('id');           // To Get Facebook ID
        $fbfullname = $profile->getProperty('name');   // To Get Facebook full name
        $fbemail = $profile->getProperty('email');    //  To Get Facebook email
        $fbpic = "<img src='" . $picture['url'] . "' class='img-rounded'/>";
        # save the user nformation in session variable
        $_SESSION['fb_id'] = $fbid . '</br>';
        $_SESSION['fb_name'] = $fbfullname . '</br>';
        $_SESSION['fb_email'] = $fbemail . '</br>';
        $_SESSION['fb_pic'] = $fbpic . '</br>';
        $_SESSION['login_time'] = time();
    } catch (Facebook\Exceptions\FacebookResponseException $e) {
        // When Graph returns an error
        echo 'Graph returned an error: ' . $e->getMessage();
        session_destroy();
        // redirecting user back to app login page
        header("Location: ./");
        exit;
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        // When validation fails or other local issues
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }

    if (isset($_GET['code'])) {
        echo '<div class="container">';
        echo '<div class="card-body">';
        echo '<h4 class="card-title">' . $_SESSION['fb_name'] . '</h4>';
        echo '<p class="card-text">' . $_SESSION['fb_email'] . '</p>';
        echo '<p>Время авторизации: ' . date('Y-m-d H:i:s', $_SESSION['login_time'] + 6 * 3600) . '</p>';
        echo '<a href="logout.php" style="text-decoration: underline">Logout</a>';
        echo '</div>';
        echo '</div>';
    }
} else {
    // replace  website URL same as added in the developers.Facebook.com/apps e.g. if you used http instead of https and used            
    $loginUrl = $helper->getLoginUrl('https://phpstack-1110056-3893223.cloudwaysapps.com/', $permissions);
    echo '<div class="container">';
    echo '<a href="' . $loginUrl . '" style="font-size: 22px; color: #00308F">Log in with Facebook!</a>';
    echo '</div>';
}
// -----------------

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test-task</title>

    <style>
        .container {
            margin: 0 100px;
        }

        span {
            padding-right: 10px;
        }

        .news-box__title-wrap {
            margin-bottom: 20px;
        }

        span:first-child {
            font-weight: 600;
        }


        ul {
            list-style: none;
            padding: 0;
            display: flex;
            flex-wrap: wrap;
        }

        li {
            flex: 1;
            width: calc(33.33% - 20px);
            margin-right: 20px;
            margin-bottom: 20px;
            text-align: center;
            display: flex;
            flex-direction: column-reverse;
            justify-content: space-between;
        }

        a {
            color: #000;
            text-decoration: none;
        }

        .news-cart__name.link:hover {
            color: blue;
        }

        .news-cart__meta-row {
            display: contents;
        }

        .news-cart__pub-time::before {
            content: "Время: ";
        }

        .news-cart__views::before {
            content: "Кол-во просмотров: ";
        }

        .news-cart__comments::before {
            content: "Комментарии: ";
        }

        img {
            margin-bottom: 10px;
        }

        p {
            margin: 0;
            padding: 0;
        }

        .news-box__btn-wrap {
            margin: 35px 0 35px 0;
            text-align: center;
        }

        .btn--big {
            height: 40px;
            padding: 0 55px;
            font-size: 16px;
        }

        .btn--big:hover {
            cursor: pointer;
        }

        .block-1 {
            background-color: #f5f5f5;
            padding: 20px;
            text-align: center;
        }

        .fb-login-button {
            margin-top: 10px;
        }

        .news-box__btn.btn.btn--grey.btn--big.js_more_news {
            display: none;
        }

        .main_link {
            display: block;
            margin-top: 10px;
            font-size: 22px;
            color: #00308F;
        }
    </style>
</head>

<body>
    <div class="container">
        <a href="photo.php" class="main_link">Генератор изображений</a>
    </div>

    <?php
    if (!is_dir('img')) {
        mkdir('img');
    }

    $rss_url = "https://diapazon.kz/category/sport/aktobe";

    $rss_content = file_get_contents($rss_url);

    if ($rss_content === false) {
        die("Не удалось загрузить RSS-ленту");
    }

    $doc = new DOMDocument();
    @$doc->loadHTML($rss_content);

    $xpath = new DOMXPath($doc);
    $sectionClassName = "col-big__news-box news-box";
    $sections = $xpath->query("//section[contains(@class, '$sectionClassName')]");


    if ($sections->length > 0) {
        echo '<div class="container">';
        foreach ($sections as $section) {
            $imgTags = $section->getElementsByTagName('img');

            $liElements = explode('</li>', $doc->saveHTML($section));

            $liIndex = 0;

            foreach ($imgTags as $img) {
                $relativeImgUrl = $img->getAttribute('src');
                $imgUrl = "https://diapazon.kz" . $relativeImgUrl;

                $pathParts = pathinfo($imgUrl);
                $fileName = 'img/' . $pathParts['basename'];

                $imageData = file_get_contents($imgUrl);
                if ($imageData !== false) {
                    file_put_contents($fileName, $imageData);

                    if (isset($liElements[$liIndex])) {
                        $liElements[$liIndex] = str_replace('href="/', 'href="https://diapazon.kz/', $liElements[$liIndex]);
                        $liElements[$liIndex] .= "<img src='$fileName' alt='Изображение'>";
                    }
                }

                $liIndex++;
            }

            foreach ($liElements as $liElement) {
                echo $liElement . '</li>';
            }
        }
        echo '</div>';
    } else {
        echo "Секция не найдена.";
    }

    ?>
</body>
<script>

</script>

</html>