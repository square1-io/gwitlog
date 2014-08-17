<html>
  <head>
    <style>
        body
        {
            background: #f9f9f9;
            font-family: "Helvetica Neue",Arial,sans-serif;
            font-size: 16px;
            margin: 0px;
        }
        ul
        {
            list-style-type: none;
            padding: 0;
            margin-top: 0;
            background: white;
            border-top: 1px solid #e1e8ed;
        }
        li
        {
            background: #fff;
            border: 1px solid #f9f9f9;
            border-bottom: 1px solid #e1e8ed;
            background-clip: padding-box;
            padding: 10px;
        }
        li:hover {
            border: 1px solid #009999;
        }
        ul li:nth-child(odd)
        {
            background: #f5f8fa;
        }
        a
        {
            color: #8899a6;
            text-decoration: none;
        }
        a:hover
        {
            color: #009999;
            text-decoration: underline;
        }
        .avatar
        {
            float: left;
            border-radius: 10px;
        }
        .gweet-body
        {
            margin-left: 90px;
        }
        .clear
        {
            clear: both;
        }
        .gweet-user
        {
            font-weight: bold;
            color: #292f33;
            float: left;
        }
        .gweet-branch, .gweet-time
        {
            color: #8899a6;
        }
        .gweet-detail, .gweet-commit
        {
            padding-top: 10px;
        }
        .gweet-commit
        {
            font-size: 14px;
            color: #8899a6;
        }
        .container
        {
            border-radius: 10px;
            background: white;
            padding-top: 10px;
            margin: 50px 10px 0px;
        }
        .container h1
        {
            padding-left: 10px;
            color: #66757f;
            font-weight: 300;
            line-height: 22px;
            margin-top: 5px;
        }
        .header-cont {
            width:100%;
            position:fixed;
            top:0px;
        }
        .header {
            height: 35px;
            color: #fdfdfd;
            background:#464d57;
            width: 100%;
            margin:0px auto;
            font-size: 20px;
            font-weight: 300;
            line-height: 20px;
            padding-top: 10px;
            padding-left: 20px;
        }
        .title {
            padding: 10px;
            margin-top: -10px;
        }
        .smallprint {
            float:right;
            margin-right: 10px;
            font-size: 10px;
        }
        .linkable {
            cursor: pointer;
        }
        </style>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/polymer/0.2.2/platform.js"></script>
        <script src="http://dev.conroyp.com/gwitlog/time-elements.js"></script>
        <script>
        $(document).ready(function () {
            $('.linkable').click(function() {
                window.location = $(this).data('linked');
            });
        });
        </script>
      </head>
    <body>
        <div class="header-cont">
            <div class="header">
                <?php
                if (isset($repo)) {
                    echo htmlentities($repo);
                }
                ?> Commit Log
            </div>
        </div>
        <div class='container'>

            <div class="smallprint">
                <a href="https://github.com/conroyp/gwitlog/">Gwitlog</a> at <?=date('H:i, l F jS, Y'); ?>.
            </div>
            <div class='title'>
                Commit history
            </div>
            <ul>