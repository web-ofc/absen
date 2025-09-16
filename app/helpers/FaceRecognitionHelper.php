<?php 
namespace App\Helpers;

class FaceRecognitionHelper
{
    /**
     * Calculate euclidean distance between two face descriptors
     */
    public static function calculateDistance($descriptor1, $descriptor2)
    {
        if (!is_array($descriptor1) || !is_array($descriptor2)) {
            return 1; // Max distance if invalid
        }
        
        if (count($descriptor1) !== count($descriptor2)) {
            return 1; // Max distance if different lengths
        }
        
        $sum = 0;
        for ($i = 0; $i < count($descriptor1); $i++) {
            $sum += pow($descriptor1[$i] - $descriptor2[$i], 2);
        }
        
        return sqrt($sum);
    }
    
    /**
     * Find matching user based on face descriptor
     */
    public static function findMatchingUser($inputDescriptor, $threshold = 0.6)
    {
        $users = \App\Models\User::whereNotNull('face_descriptor')
                                ->where('is_active', true)
                                ->get();
        
        $bestMatch = null;
        $bestDistance = 1;
        
        foreach ($users as $user) {
            $userDescriptor = json_decode($user->face_descriptor, true);
            $distance = self::calculateDistance($inputDescriptor, $userDescriptor);
            
            if ($distance < $threshold && $distance < $bestDistance) {
                $bestMatch = $user;
                $bestDistance = $distance;
            }
        }
        
        return $bestMatch;
    }
}