<?php
// test-songs.php

// include files
require_once 'Database/Connection.php';
require_once 'Models/Artist.php';
require_once 'Models/Song.php';
require_once 'Repositories/BaseRepository.php';
require_once 'Repositories/ArtistRepository.php';
require_once 'Repositories/SongRepository.php';

use Models\Artist;
use Models\Song;
use Repositories\ArtistRepository;
use Repositories\SongRepository;

echo "=== Testing SongRepository ===\n\n";

try {
    $artistRepo = new ArtistRepository();
    $songRepo = new SongRepository();
    
    // first, we need an artist to assign songs to
    echo "0. Creating an artist first (for foreign key)...\n";
    $artist = new Artist(
        null,
        'Queen',
        'British rock band',
        null,
        null,
        null
    );
    $createdArtist = $artistRepo->create($artist);
    echo "   Created artist: " . $createdArtist->getName() . " (ID: " . $createdArtist->getId() . ")\n\n";
    
    // 1. create a song
    echo "1. Creating a song...\n";
    $song = new Song(
        null,
        'Bohemian Rhapsody',
        $createdArtist->getId(),  // foreign key to artist!
        'A Night at the Opera',
        'Rock',
        null,
        null
    );
    $createdSong = $songRepo->create($song);
    echo "   Created: " . $createdSong->getTitle() . " (ID: " . $createdSong->getId() . ")\n\n";
    
    // 2. create another song for same artist
    echo "2. Creating another song...\n";
    $song2 = new Song(
        null,
        'We Will Rock You',
        $createdArtist->getId(),
        'News of the World',
        'Rock',
        null,
        null
    );
    $createdSong2 = $songRepo->create($song2);
    echo "   Created: " . $createdSong2->getTitle() . "\n\n";
    
    // 3. find all songs by artist
    echo "3. Finding all songs by artist...\n";
    $artistSongs = $songRepo->findByArtistId($createdArtist->getId());
    echo "   Found " . count($artistSongs) . " songs:\n";
    foreach ($artistSongs as $s) {
        echo "   - " . $s->getTitle() . "\n";
    }
    echo "\n";
    
    // 4. get song with artist info
    echo "4. Getting song with artist info (JOIN)...\n";
    $songWithArtist = $songRepo->findByIdWithArtist($createdSong->getId());
    echo "   Song: " . $songWithArtist['title'] . " by " . $songWithArtist['artist_name'] . "\n\n";
    
    // 5. try to create song with invalid artist
    echo "5. Testing foreign key constraint...\n";
    try {
        $badSong = new Song(null, 'Bad Song', 9999, 'Album', 'Genre', null, null);
        $songRepo->create($badSong);
    } catch (PDOException $e) {
        echo "   ✓ Correctly rejected: " . $e->getMessage() . "\n\n";
    }
    
    // 6. cleanup
    echo "6. Cleaning up...\n";
    $songRepo->delete($createdSong->getId());
    $songRepo->delete($createdSong2->getId());
    $artistRepo->delete($createdArtist->getId());
    echo "   Done!\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}