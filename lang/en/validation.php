<?php

return [

    'accepted' => 'The :attribute must be accepted.',
    'confirmed' => 'The :attribute confirmation does not match.',
    'current_password' => 'The current password is incorrect.',
    'date' => 'The :attribute must be a valid date.',
    'email' => 'Enter a valid email address.',
    'exists' => 'The selected :attribute is invalid.',
    'image' => 'The :attribute must be an image file.',
    'in' => 'The selected :attribute is invalid.',
    'integer' => 'The :attribute must be a number.',
    'max' => [
        'file' => 'The :attribute may not be greater than :max kilobytes.',
        'numeric' => 'The :attribute may not be greater than :max.',
        'string' => 'The :attribute may not be greater than :max characters.',
    ],
    'min' => [
        'string' => 'The :attribute must be at least :min characters.',
    ],
    'required' => 'The :attribute field is required.',
    'unique' => 'This :attribute is already taken.',
    'uploaded' => 'The :attribute failed to upload. Please try again.',

    'attributes' => [
        'name' => 'name',
        'email' => 'email',
        'password' => 'password',
        'password_confirmation' => 'password confirmation',
        'current_password' => 'current password',
        'phone' => 'phone number',
        'address' => 'address',
        'item_name' => 'item name',
        'item_description' => 'description',
        'item_type' => 'item type',
        'item_image' => 'photo',
        'category_id' => 'category',
        'location_id' => 'location',
        'lost_or_found_date' => 'date',
        'status' => 'status',
        'claim_message' => 'claim message',
        'proof_description' => 'proof details',
        'category_name' => 'category name',
        'location_name' => 'location name',
        'board_view' => 'display preference',
    ],

    'custom' => [
        'email' => [
            'unique' => 'An account with this email already exists.',
        ],
        'password' => [
            'confirmed' => 'The password confirmation does not match.',
        ],
    ],

];
