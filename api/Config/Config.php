<?php
const BASE_URL = "http://localhost/torneos/api/";
const APP_URL = "http://localhost/torneos/app/";

// Zona horaria
date_default_timezone_set('America/Bogota');

// Datos de conexión a Base de Datos
const DB_HOST = "localhost";
const DB_NAME = "db-globalcup";
const DB_USER = "root";
const DB_PASSWORD = "";
const DB_CHARSET = "utf8";

// Para envío de correo
const ENVIRONMENT = 1; // 1: Local, 0: Producción

// JWT Configuration
const JWT_SECRET = "globalcup_secret_key_2024";
const JWT_ALGO = 'HS256';
?>