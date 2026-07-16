<?php

function buildMeta($type, $data = [])
{
    $base_url = "https://site.com"; // dəyiş

    $meta = [
        'title' => 'Default title',
        'description' => 'Default description',
        'image' => $base_url . '/assets/img/default.jpg',
        'url' => $base_url,
        'type' => 'website'
    ];

    switch ($type) {

        case 'listing':
            $meta['title'] = $data['title'] ?? 'Elan';
            $meta['description'] = $data['description'] ?? '';
            $meta['image'] = !empty($data['image'])
                ? $base_url . '/uploads/' . $data['image']
                : $meta['image'];
            $meta['url'] = $base_url . '/listing/' . $data['slug'];
            $meta['type'] = 'article';
            break;

        case 'listings':
            $meta['title'] = 'Bütün elanlar';
            $meta['description'] = 'Saytdakı bütün elanları kəşf et';
            $meta['url'] = $base_url . '/listings';
            break;

        case 'home':
            $meta['title'] = 'Ana səhifə';
            $meta['description'] = 'Ən son elanlar və xidmətlər';
            break;

        case 'category':
            $meta['title'] = $data['name'] . ' elanları';
            $meta['description'] = $data['name'] . ' kateqoriyasında elanlar';
            $meta['url'] = $base_url . '/category/' . $data['slug'];
            break;
    }

    return $meta;
}