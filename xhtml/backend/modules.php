<?php
declare(strict_types=1);

return [
    'classes' => [
        'table' => 'classes',
        'primary_key' => 'id',
        'title' => 'name',
        'fields' => [
            'name' => ['label' => 'Class Name', 'type' => 'text', 'required' => true],
            'description' => ['label' => 'Description', 'type' => 'textarea', 'required' => false],
            'start_date' => ['label' => 'Start Date', 'type' => 'date', 'required' => false],
            'price' => ['label' => 'Base Price', 'type' => 'number', 'required' => false],
            'instructor' => ['label' => 'Instructor', 'type' => 'text', 'required' => false],
            'image_path' => ['label' => 'Image', 'type' => 'file', 'required' => false],
            // Booking related settings
            'booking_enabled' => ['label' => 'Enable Booking', 'type' => 'number', 'required' => false],
            'booking_type' => ['label' => 'Booking Type', 'type' => 'text', 'required' => false],
            'slot_capacity' => ['label' => 'Slot Capacity', 'type' => 'number', 'required' => false],
            'timeslots' => ['label' => 'Time Slots', 'type' => 'text', 'required' => false],
            'tiers' => ['label' => 'Program Tiers', 'type' => 'text', 'required' => false],
        ],
    ],
    'events' => [
        'table' => 'events',
        'primary_key' => 'id',
        'title' => 'title',
        'fields' => [
            'title' => ['label' => 'Event Title', 'type' => 'text', 'required' => true],
            'event_date' => ['label' => 'Event Date', 'type' => 'date', 'required' => true],
            'event_time' => ['label' => 'Event Time', 'type' => 'time', 'required' => false],
            'location' => ['label' => 'Location', 'type' => 'text', 'required' => false],
            'description' => ['label' => 'Description', 'type' => 'textarea', 'required' => false],
            'highlights' => ['label' => 'Highlights', 'type' => 'textarea', 'required' => false],
            'image_path' => ['label' => 'Image', 'type' => 'file', 'required' => false],
        ],
    ],
    'gallery' => [
        'table' => 'gallery',
        'primary_key' => 'id',
        'title' => 'title',
        'fields' => [
            'title' => ['label' => 'Title', 'type' => 'text', 'required' => true],
            'category' => ['label' => 'Category', 'type' => 'text', 'required' => false],
            'description' => ['label' => 'Description', 'type' => 'textarea', 'required' => false],
            'image_path' => ['label' => 'Image', 'type' => 'file', 'required' => false],
            'video_path' => ['label' => 'Video', 'type' => 'file', 'required' => false],
        ],
    ],
    'blogs' => [
        'table' => 'blogs',
        'primary_key' => 'id',
        'title' => 'title',
        'fields' => [
            'title' => ['label' => 'Blog Title', 'type' => 'text', 'required' => true],
            'author' => ['label' => 'Author Name', 'type' => 'text', 'required' => true],
            'category' => ['label' => 'Category', 'type' => 'text', 'required' => true],
            'publish_date' => ['label' => 'Publish Date', 'type' => 'date', 'required' => true],
            'content' => ['label' => 'Content', 'type' => 'textarea', 'required' => true],
            'image_path' => ['label' => 'Thumbnail Image', 'type' => 'file', 'required' => false],
        ],
    ],
    'bookings' => [
        'table' => 'bookings',
        'primary_key' => 'id',
        'title' => 'customer_name',
        'fields' => [
            'class_id' => ['label' => 'Class ID', 'type' => 'number', 'required' => true],
            'customer_name' => ['label' => 'Customer Name', 'type' => 'text', 'required' => true],
            'customer_email' => ['label' => 'Customer Email', 'type' => 'text', 'required' => true],
            'customer_phone' => ['label' => 'Customer Phone', 'type' => 'text', 'required' => false],
            'program_tier' => ['label' => 'Program Tier', 'type' => 'text', 'required' => false],
            'booking_date' => ['label' => 'Booking Date', 'type' => 'date', 'required' => true],
            'time_slot' => ['label' => 'Time Slot', 'type' => 'text', 'required' => true],
            'quantity' => ['label' => 'Quantity', 'type' => 'number', 'required' => true],
            'personal_or_group' => ['label' => 'Type', 'type' => 'text', 'required' => false],
            'payment_status' => ['label' => 'Payment Status', 'type' => 'text', 'required' => false],
            'transaction_id' => ['label' => 'Transaction ID', 'type' => 'text', 'required' => false],
        ],
    ],
];
