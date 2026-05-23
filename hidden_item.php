<?php

/**
 * Task 2: Hidden Item Game
 *
 * Program ini mensimulasikan pencarian item tersembunyi di dalam sebuah grid.
 * Pemain mulai dari posisi X, lalu bergerak sesuai urutan langkah yang diberikan:
 *   1. Naik ke atas sebanyak A langkah
 *   2. Geser ke kanan sebanyak B langkah
 *   3. Turun ke bawah sebanyak C langkah
 *
 * Setiap posisi yang dilewati saat turun ke bawah dianggap sebagai
 * kemungkinan lokasi item tersembunyi.
 *
 * Cara pakai:
 *   php hidden_item.php <A> <B> <C>
 *
 * Contoh:
 *   php hidden_item.php 2 3 1
 */

// definisi grid permainan
// '#' = tembok/rintangan, '.' = jalur bebas, 'X' = posisi awal pemain
$grid = [
    ['#','#','#','#','#','#','#','#'],
    ['#','.','.','.','.','.','.','#'],
    ['#','.','#','#','#','.','.','#'],
    ['#','.','.','.','#','.','#','#'],
    ['#','X','#','.','.','.','.',  '#'],
    ['#','#','#','#','#','#','#','#'],
];

// cari posisi awal pemain (X) di dalam grid
$startRow = null;
$startCol = null;

foreach ($grid as $r => $row) {
    foreach ($row as $c => $cell) {
        if ($cell === 'X') {
            $startRow = $r;
            $startCol = $c;
        }
    }
}

// kalau posisi X tidak ditemukan, program tidak bisa jalan
if ($startRow === null) {
    echo "Error: Posisi awal X tidak ditemukan di dalam grid.\n";
    exit(1);
}

// pastikan pengguna memasukkan tepat 3 argumen (A, B, C)
if ($argc !== 4) {
    echo "Cara pakai: php hidden_item.php <A> <B> <C>\n";
    echo "  A = jumlah langkah ke atas\n";
    echo "  B = jumlah langkah ke kanan\n";
    echo "  C = jumlah langkah ke bawah\n";
    exit(1);
}

// ambil nilai langkah dari argumen command line
$A = (int) $argv[1]; // langkah ke atas
$B = (int) $argv[2]; // langkah ke kanan
$C = (int) $argv[3]; // langkah ke bawah

$rows      = count($grid);
$cols      = count($grid[0]);
$probables = []; // daftar koordinat yang kemungkinan besar adalah lokasi item

/**
 * Fungsi untuk menggerakkan pemain ke satu arah sebanyak N langkah.
 *
 * Pergerakan akan berhenti lebih awal jika:
 * - Pemain mencapai batas tepi grid
 * - Pemain menabrak tembok (#)
 *
 * Mengembalikan posisi terakhir yang bisa dicapai.
 */
function move(array $grid, int $row, int $col, int $dRow, int $dCol, int $steps): array
{
    for ($i = 0; $i < $steps; $i++) {
        $nextRow = $row + $dRow;
        $nextCol = $col + $dCol;

        // berhenti kalau keluar dari grid atau ketemu tembok
        if (
            $nextRow < 0 || $nextRow >= count($grid) ||
            $nextCol < 0 || $nextCol >= count($grid[0]) ||
            $grid[$nextRow][$nextCol] === '#'
        ) {
            break;
        }

        // posisi aman, lanjutkan pergerakan
        $row = $nextRow;
        $col = $nextCol;
    }

    return [$row, $col];
}

// langkah 1: bergerak ke atas sebanyak A langkah
[$afterUp_row, $afterUp_col] = move($grid, $startRow, $startCol, -1, 0, $A);

// langkah 2: bergerak ke kanan sebanyak B langkah
[$afterRight_row, $afterRight_col] = move($grid, $afterUp_row, $afterUp_col, 0, 1, $B);

// langkah 3: bergerak ke bawah sebanyak C langkah
// setiap sel yang diinjak saat turun dianggap sebagai lokasi kemungkinan item
$curRow = $afterRight_row;
$curCol = $afterRight_col;

for ($i = 0; $i < $C; $i++) {
    $nextRow = $curRow + 1;
    $nextCol = $curCol;

    // berhenti kalau sudah di batas bawah grid atau ada tembok
    if (
        $nextRow >= $rows ||
        $grid[$nextRow][$nextCol] === '#'
    ) {
        break;
    }

    $curRow = $nextRow;

    // mencatat sebagai lokasi kemungkinan kalau selnya adalah jalur bebas
    if ($grid[$curRow][$curCol] === '.' || $grid[$curRow][$curCol] === 'X') {
        $probables[] = [$curRow, $curCol];
    }
}

// kalau tidak ada pergerakan ke bawah sama sekali,
// posisi saat ini langsung dianggap sebagai lokasi kemungkinan
if (empty($probables)) {
    $probables[] = [$curRow, $curCol];
}

// tampilkan hasil pencarian
echo "Posisi awal  : (baris={$startRow}, kolom={$startCol})\n";
echo "Langkah      : Atas={$A}, Kanan={$B}, Bawah={$C}\n";
echo "\nKemungkinan lokasi item:\n";

foreach ($probables as $point) {
    echo "  -> (baris={$point[0]}, kolom={$point[1]})\n";
}

// menampilkan grid dengan tanda $ di lokasi kemungkinan item
echo "\nGrid ($ = kemungkinan lokasi item):\n\n";

$displayGrid = $grid;

foreach ($probables as $point) {
    $displayGrid[$point[0]][$point[1]] = '$';
}

foreach ($displayGrid as $row) {
    echo implode('', $row) . "\n";
}

echo "\n";