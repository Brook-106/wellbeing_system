<?php

function sanitize($data)
{
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function redirect($url)
{
    header("Location: $url");
    exit();
}

function showStatus($status)
{
    if($status=="Active"){
        return "<span class='badge bg-success'>Active</span>";
    }

    return "<span class='badge bg-danger'>Inactive</span>";
}