<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AboutUsController extends Controller
{
    public function index()
    {
        // Aqui você buscaria os dados do seu banco de dados
        // Por exemplo, de uma tabela 'settings' ou 'about_pages'
        // Por enquanto, vamos retornar dados mockados (inventados)

        $aboutInfo = [
            'name' => 'QuickDish',
            'description' => 'Bem-vindo ao QuickDish, onde a culinária encontra a paixão. Nossa jornada começou em 2020 com a visão de trazer sabores autênticos e inovação para a sua mesa. Cada prato é uma celebração de ingredientes frescos e técnicas aprimoradas.',
            'history' => 'Fundado pelo Chef João Silva, o QuickDish rapidamente se tornou um ponto de referência gastronômico na cidade, conhecido por sua atmosfera acolhedora e pratos inesquecíveis que combinam tradição e modernidade.',
            'address' => 'Rua da Gastronomia, 123, Centro - Muriaé, MG',
            'phone' => '(32) 98765-4321',
            'email' => 'contato@quickdish.com.br',
            'image_url' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
            'mission' => 'Nossa missão é proporcionar uma experiência gastronômica memorável, combinando inovação, qualidade e um serviço excepcional, celebrando a arte da culinária em cada detalhe.',
            'values' => ['Qualidade', 'Paixão', 'Inovação', 'Hospitalidade', 'Sustentabilidade']
        ];

        return response()->json($aboutInfo);
    }
}