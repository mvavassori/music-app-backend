<?php
require_once 'Database/Connection.php';
require_once 'Models/Artist.php';
require_once 'Repositories/BaseRepository.php';
require_once 'Repositories/ArtistRepository.php';

use Models\Artist;
use Repositories\ArtistRepository;

echo "=== Testing ArtistRepository ===\n\n";

try {
    $artistRepo = new ArtistRepository();
    
    // 1. create an artist
    echo "1. Creating artist...\n";
    $artist = new Artist(
        null,
        'The Beatles',
        'Legendary British rock band',
        'beatles.jpg',
        null,
        null
    );
    
    $created = $artistRepo->create($artist);
    echo "   Created: " . $created->getName() . " (ID: " . $created->getId() . ")\n\n";
    
    // 2. find by id
    echo "2. Finding artist by ID...\n";
    $found = $artistRepo->findById($created->getId());
    echo "   Found: " . $found->getName() . "\n\n";
    
    // 3. update artist
    echo "3. Updating artist...\n";
    $found->setBio('The best band ever!');
    $updated = $artistRepo->update($found);
    echo "   New bio: " . $updated->getBio() . "\n\n";
    
    // 4. get all artists
    echo "4. Getting all artists...\n";
    $allArtists = $artistRepo->findAll();
    echo "   Total artists: " . count($allArtists) . "\n";
    foreach ($allArtists as $a) {
        echo "   - " . $a->getName() . "\n";
    }
    echo "\n";
    
    // 5. delete artist
    echo "5. Deleting artist...\n";
    if ($artistRepo->delete($created->getId())) {
        echo "   Deleted successfully!\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}