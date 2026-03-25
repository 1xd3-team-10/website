<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION["username"]) && isset($_SESSION["fullName"]) && isset($_SESSION["email"]);
}

function getUsername(): string | null {
    if (!isset($_SESSION["username"])) return null;
    return $_SESSION["username"];
}

function getFullName(): string | null {
    if (!isset($_SESSION["fullName"])) return null;
    return $_SESSION["fullName"];
}

function getEmail(): string | null {
    if (!isset($_SESSION["email"])) return null;
    return $_SESSION["email"];
}