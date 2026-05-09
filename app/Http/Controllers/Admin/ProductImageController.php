<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    /**
     * Hapus foto produk secara spesifik (AJAX)
     */
    public function destroy(ProductImage $image)
    {
        try {
            // 1. Hapus file fisik
            if ($image->image_path && Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }

            // 2. Hapus dari database (Gunakan delete langsung dari instance)
            \App\Models\ProductImage::destroy($image->id);

            return response()->json([
                'success' => true,
                'message' => 'Foto batik berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            // Jika error tetap muncul, coba paksa hapus berdasarkan ID:
            // ProductImage::destroy($image->id); 
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus: ' . $e->getMessage()
            ], 500);
        }
    }
}