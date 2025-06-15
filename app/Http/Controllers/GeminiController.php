<?php

namespace App\Http\Controllers;

use App\Models\CartProduct;
use App\Models\ChatHistory;
use App\Models\Favourite;
use App\Models\Product;
use GeminiAPI\Laravel\Facades\Gemini;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GeminiController extends Controller
{

    protected $formattedHistory = [];
    public function sendMessage(Request $request)
    {
        $message = $request->input('message');
        // Process the message and generate a reply
        $note1="If my message is greating message you should reply normally ";


        $reply =Gemini::generateText("Check first if the following message is related to decoration and interior design if yes give me response (Note : Do not mention that my message is related to decoration and interior design in each time my message is related to them)
                                             and if not tell me sorry I cannot help you ! but $note1. my message is : ".$message);

        //$productArr=Gemini::generateText($note2." This is user message : ".$message);

        return response()->json(['reply' => $reply]);
    }

//----------------------------------------------------------------------------------------------------------
    public function handlePrompt(Request $request)
    {
        $userPrompt = $request->input('message');


        $user = $request->user();

        $histories = ChatHistory::where('user_id', $user->id)
            ->orderBy('created_at')
            ->get();



        foreach ($histories as $chat) {
            $this->formattedHistory[] = [
                'message' => $chat->user_message,
                'role' => 'user',
            ];
            $this->formattedHistory[] = [
                'message' => $chat->bot_response,
                'role' => 'model',
            ];
        }

        try {
            $geminiResponse = $this->processWithGemini($userPrompt);
            $intent = $geminiResponse['intent'] ?? null;
            $entities = $geminiResponse['entities'] ?? [];

            Log::info('Gemini response:', (array)$geminiResponse);

            if ($intent === 'Product Inquiry') {
                $productsHtml = $this->getHomeDecorProducts($entities);

                ChatHistory::create([
                    'user_id' => auth()->id(),
                    'user_message' => $userPrompt,
                    'bot_response' => is_array($productsHtml) ? json_encode($productsHtml) : $productsHtml,
                ]);

                Log::info('Reply content type:', ['type' => gettype($productsHtml), 'content' => $productsHtml]);

                return response()->json([
                    'reply' => $productsHtml
                ]);
            }
            elseif ($intent === 'Add Product To Cart') {
                $addedHtml = $this->getProductsToAddToCart($entities);

                ChatHistory::create([
                    'user_id' => auth()->id(),
                    'user_message' => $userPrompt,
                    'bot_response' => is_array($addedHtml) ? json_encode($addedHtml) : $addedHtml,
                ]);
                Log::info('Reply content type:', ['type' => gettype($addedHtml), 'content' => $addedHtml]);
                return response()->json([
                    'reply' => $addedHtml
                ]);
            }
            elseif ($intent === 'Add Product To Favorites') {
                $addedHtml = $this->getProductsToAddToFavorites($entities);

                ChatHistory::create([
                    'user_id' => auth()->id(),
                    'user_message' => $userPrompt,
                    'bot_response' => is_array($addedHtml) ? json_encode($addedHtml) : $addedHtml,
                ]);
                Log::info('Reply content type:', ['type' => gettype($addedHtml), 'content' => $addedHtml]);

                return response()->json([
                    'reply' => $addedHtml,
                ]);
            }
            elseif ($this->isServiceInquiry($userPrompt)) {
                $services="
                    Our services :
                    AI Chatbot Assistance
- Smart assistant to help users search for products, ask décor-related questions, and manage their cart or favorites using natural language.

- 3D Product Visualization
See how furniture will look in your space with interactive 3D previews.

- Personalized Shopping Experience
Get product recommendations tailored to your style and preferences.

- Support for Buyers & Sellers
Tools and dashboards designed for both customers and vendors to manage their experience.

- Wishlist & Smart Cart
Easily add products to your cart or favorites through the chatbot or browsing interface.
                    ";

                ChatHistory::create([
                    'user_id' => auth()->id(),
                    'user_message' => $userPrompt,
                    'bot_response' => $services,
                ]);

                return response()->json([
                    'reply' => $services,
                ]);
            }

            $note = "If my message is a greeting message, you should reply normally.";
            $geminiReply = Gemini::generateText(
                "Check first if the following message is related to decoration and interior design. ".
                "If yes, give me a response (Note: Do not mention that my message is related to decoration and interior design every time). ".
                "If not, say: Sorry, I cannot help you! But $note. ".
                "My message is: " . $userPrompt
            );

            ChatHistory::create([
                'user_id' => auth()->id(),
                'user_message' => $userPrompt,
                'bot_response' => is_array($geminiReply) ? json_encode($geminiReply) : $geminiReply,
            ]);

            return response()->json(['reply' => $geminiReply]);

        } catch (\Throwable $e) {
            Log::error("Prompt handling error: " . $e->getMessage());
            return response()->json(['reply' => 'Oops! Something went wrong. Please try again later.'], 500);
        }
    }


    //-------------------------------------------------------------------------------------------------

    //---------------------------------------------------------------
    private function processWithGemini(string $userPrompt)
    {
        $productInquiryExamples = [
            [
                'intent' => 'Product Inquiry',
                'entities' => [
                    'product_name' => 'chair gh',
                    'category' => 'Bedroom',
                    'brand' => 'HC',
                    'color' => 'White',
                    'height' => 150,
                    'width' => 150,
                    'price_min' => 100,
                    'price_max' => 500,
                    'number_of_items' => 3,
                    'sale_min' => 10,
                    'sale_max' => 50,
                ],
            ],
            [
                'intent' => 'Product Inquiry',
                'entities' => [
                    'product_name' => 'sofa elite',
                    'category' => 'Living Room',
                    'brand' => 'ComfortLux',
                    'color' => 'Gray',
                    'height' => 200,
                    'width' => 300,
                    'price_min' => 250,
                    'price_max' => 1000,
                    'number_of_items' => 1,
                    'sale_min' => null,
                    'sale_max' => null,
                ],
            ],
            [
                'intent' => 'Product Inquiry',
                'entities' => [
                    'product_name' => 'dining table',
                    'category' => 'Dining Room',
                    'brand' => null,
                    'color' => 'Brown',
                    'height' => 100,
                    'width' => 180,
                    'price_min' => 300,
                    'price_max' => 800,
                    'number_of_items' => 1,
                    'sale_min' => 5,
                    'sale_max' => 15,
                ],
            ],
            [
                'intent' => 'Product Inquiry',
                'entities' => [
                    'product_name' => 'bathroom cabinet',
                    'category' => 'Bathroom',
                    'brand' => 'AquaSpace',
                    'color' => 'White',
                    'height' => null,
                    'width' => null,
                    'price_min' => 120,
                    'price_max' => 350,
                    'number_of_items' => 2,
                    'sale_min' => 10,
                    'sale_max' => 20,
                ],
            ],
            [
                'intent' => 'Product Inquiry',
                'entities' => [
                    'product_name' => 'office desk',
                    'category' => 'Office',
                    'brand' => 'WorkWell',
                    'color' => 'Black',
                    'height' => 120,
                    'width' => 200,
                    'price_min' => 180,
                    'price_max' => 600,
                    'number_of_items' => 1,
                    'sale_min' => null,
                    'sale_max' => null,
                ],
            ],
            [
                'intent' => 'Product Inquiry',
                'entities' => [
                    'product_name' => 'garden bench',
                    'category' => 'Outdoor',
                    'brand' => 'NatureComfort',
                    'color' => 'Green',
                    'height' => 90,
                    'width' => 150,
                    'price_min' => 150,
                    'price_max' => 400,
                    'number_of_items' => 3,
                    'sale_min' => 5,
                    'sale_max' => 30,
                ],
            ],
            [
                'intent' => 'Product Inquiry',
                'entities' => [
                    'product_name' => 'rug comfort',
                    'category' => 'Rugs',
                    'brand' => 'SoftStep',
                    'color' => 'Beige',
                    'height' => null,
                    'width' => null,
                    'price_min' => 75,
                    'price_max' => 250,
                    'number_of_items' => 2,
                    'sale_min' => 10,
                    'sale_max' => 40,
                ],
            ],
            [
                'intent' => 'Product Inquiry',
                'entities' => [
                    'product_name' => 'wall painting',
                    'category' => 'Home Decor',
                    'brand' => 'ArtVibe',
                    'color' => 'Multicolor',
                    'height' => 60,
                    'width' => 90,
                    'price_min' => 40,
                    'price_max' => 150,
                    'number_of_items' => 1,
                    'sale_min' => null,
                    'sale_max' => 20,
                ],
            ],
            [
                'intent' => 'Product Inquiry',
                'entities' => [
                    'product_name' => 'floor lamp',
                    'category' => 'Lighting',
                    'brand' => null,
                    'color' => 'Black',
                    'height' => 180,
                    'width' => null,
                    'price_min' => 60,
                    'price_max' => 220,
                    'number_of_items' => 4,
                    'sale_min' => 10,
                    'sale_max' => 25,
                ],
            ],
            [
                'intent' => 'Product Inquiry',
                'entities' => [
                    'product_name' => 'wardrobe classic',
                    'category' => 'Bedroom',
                    'brand' => 'RoyalWood',
                    'color' => 'Brown',
                    'height' => 210,
                    'width' => 250,
                    'price_min' => 500,
                    'price_max' => 1200,
                    'number_of_items' => 1,
                    'sale_min' => 15,
                    'sale_max' => 45,
                ],
            ],
        ];


        $addToCartExamples = [
            [
                'intent' => 'Add Product To Cart',
                'entities' => [
                    'name' => 'modern sofa',
                    'quantity' => '1',
                ],
            ],
            [
                'intent' => 'Add Product To Cart',
                'entities' => [
                    'name' => 'ceramic vase set',
                    'quantity' => '3',
                ],
            ],
            [
                'intent' => 'Add Product To Cart',
                'entities' => [
                    'name' => 'wooden dining table',
                    'quantity' => '1',
                ],
            ],
            [
                'intent' => 'Add Product To Cart',
                'entities' => [
                    'name' => 'floor lamp classic',
                    'quantity' => '2',
                ],
            ],
            [
                'intent' => 'Add Product To Cart',
                'entities' => [
                    'name' => 'office chair ergonomic',
                    'quantity' => '1',
                ],
            ],
            [
                'intent' => 'Add Product To Cart',
                'entities' => [
                    'name' => 'outdoor bench metal',
                    'quantity' => '1',
                ],
            ],
            [
                'intent' => 'Add Product To Cart',
                'entities' => [
                    'name' => 'bedroom wardrobe',
                    'quantity' => '1',
                ],
            ],
            [
                'intent' => 'Add Product To Cart',
                'entities' => [
                    'name' => 'cotton rug large',
                    'quantity' => '2',
                ],
            ],
            [
                'intent' => 'Add Product To Cart',
                'entities' => [
                    'name' => 'decorative wall art',
                    'quantity' => '4',
                ],
            ],
            [
                'intent' => 'Add Product To Cart',
                'entities' => [
                    'name' => 'glass coffee table',
                    'quantity' => '1',
                ],
            ],
        ];


        $addToFavoritesExamples = [
            [
                'intent' => 'Add Product To Favorites',
                'entities' => [
                    'name' => 'modern sofa',
                ],
            ],
            [
                'intent' => 'Add Product To Favorites',
                'entities' => [
                    'name' => 'ceramic vase set',
                ],
            ],
            [
                'intent' => 'Add Product To Favorites',
                'entities' => [
                    'name' => 'wooden dining table',
                ],
            ],
            [
                'intent' => 'Add Product To Favorites',
                'entities' => [
                    'name' => 'floor lamp classic',
                ],
            ],
            [
                'intent' => 'Add Product To Favorites',
                'entities' => [
                    'name' => 'office chair ergonomic',
                ],
            ],
            [
                'intent' => 'Add Product To Favorites',
                'entities' => [
                    'name' => 'outdoor bench metal',
                ],
            ],
            [
                'intent' => 'Add Product To Favorites',
                'entities' => [
                    'name' => 'bedroom wardrobe',
                ],
            ],
            [
                'intent' => 'Add Product To Favorites',
                'entities' => [
                    'name' => 'cotton rug large',
                ],
            ],
            [
                'intent' => 'Add Product To Favorites',
                'entities' => [
                    'name' => 'decorative wall art',
                ],
            ],
            [
                'intent' => 'Add Product To Favorites',
                'entities' => [
                    'name' => 'glass coffee table',
                ],
            ],
        ];
        $prompt = "Our chat history: " . json_encode($this->formattedHistory);

        if ($this->isProductInquiry($userPrompt)) {
            $prompt .= "If the user only asks about a product and does not want to add it to the cart or favorites, you should restructure the prompt similarly to the following examples:\n";
            foreach ($productInquiryExamples as $example) {
                $prompt .= json_encode($example) . "\n";
            }
            $prompt .= "User prompt is: \"$userPrompt\"\nNote: if any of the product properties is unspecified, leave it empty.";
        } elseif ($this->addToCartCheck($userPrompt)) {
            $prompt .= "If the message asks to add a product to the cart, you should restructure the prompt similarly to the following examples:\n";
            foreach ($addToCartExamples as $example) {
                $prompt .= json_encode($example) . "\n";
            }
            $prompt .= "User prompt is: \"$userPrompt\"\nNote: if any of the product properties is unspecified, leave it empty. Product names may consist of two or more syllables; you should extract them accurately.";
        } elseif ($this->addToFavoritesCheck($userPrompt)) {
            $prompt .= "If the message asks to add a product to the favorites, you should restructure the prompt similarly to the following examples:\n";
            foreach ($addToFavoritesExamples as $example) {
                $prompt .= json_encode($example) . "\n";
            }
            $prompt .= "User prompt is: \"$userPrompt\"\nNote: if the product name is unspecified, leave it empty. Product names may consist of two or more syllables; you should extract them accurately.";
        } else {
            return null;
        }


        $reply = Gemini::generateText($prompt);

        return json_decode($reply, true);
    }

    //---------------------------------------------------------------

    public function getProductsToAddToCart(array $criteria)
    {
        $quantity=1;
        $query = Product::query();


        $userId = Auth::id();

        $productData = [];

        // Check if each criterion exists in the array, and apply a conditional filter
        if (!empty($criteria['name'])) {
            $query->where('name', 'LIKE', '%' . $criteria['name'] . '%');
        }
        if (!empty($criteria['quantity'])) {
            $quantity = $criteria['quantity'];
        }

        $products = $query->get();
        $productsNum=$query->count();
        // Format the results as an unordered list
        if ($products->isEmpty()) {
            return response()->json([
                'status' => 'empty',
                'message' => 'No products found matching the criteria from add to cart.',
                'products' => [],
            ]);
        }


        foreach ($products as $product) {
            // حفظ في السلة
            CartProduct::updateOrCreate(
                [
                    'user_id' => $userId,
                    'product_id' => $product->id,
                ],
                [
                    'quantity' => $quantity,
                ]
            );

            $productData = [
                'id' => $product->id,
                'name' => $product->name,
                'url' => route('productDetails', $product->id),
                'category' => $product->category->name ?? 'Unknown',
                'brand' => $product->brand->name ?? 'Unknown',
                'color' => $product->color ?? null,
                'width' => $product->width ?? null,
                'height' => $product->height ?? null,
                'price' => number_format($product->price, 2),
                'message' => '✔️ Product added to cart successfully!'
            ];


        }

        return $productData;

    }


    //---------------------------------------------------------------------------------------

    public function getProductsToAddToFavorites(array $criteria)
    {
        $query = Product::query();
        $userId = Auth::id();
        $productData = [];

        // Apply search criteria
        if (!empty($criteria['name'])) {
            $query->where('name', 'LIKE', '%' . $criteria['name'] . '%');
        }

        $products = $query->get();

        if ($products->isEmpty()) {
            return response()->json([
                'status' => 'empty',
                'message' => 'No products found matching the criteria for favorites.',
                'products' => [],
            ]);
        }

        foreach ($products as $product) {
            // إضافة إلى المفضلة
            Favourite::updateOrCreate(
                [
                    'user_id' => $userId,
                    'product_id' => $product->id,
                ]
            );

            $productData = [
                'id' => $product->id,
                'name' => $product->name,
                'url' => route('productDetails', $product->id),
                'category' => $product->category->name ?? 'Unknown',
                'brand' => $product->brand->name ?? 'Unknown',
                'color' => $product->color ?? null,
                'width' => $product->width ?? null,
                'height' => $product->height ?? null,
                'price' => number_format($product->price, 2),
            ];
        }

        return $productData;
    }

    //------------------------------------------------------------------------------------------------

    // To use in getting products from database -----------------------------------
    public function getHomeDecorProducts(array $criteria)
    {
        // Build the query dynamically
        $query = Product::query();

        if (!empty($criteria['name'])) {
            $query->where('name', 'LIKE', '%' . $criteria['name'] . '%');
        }

        if (!empty($criteria['category'])) {
            $query->whereHas('category', function ($q) use ($criteria) {
                $q->where('name', 'LIKE', '%' . $criteria['category'] . '%');
            });
        }

        if (!empty($criteria['brand'])) {
            $query->whereHas('brand', function ($q) use ($criteria) {
                $q->where('name', 'LIKE', '%' . $criteria['brand'] . '%');
            });
        }

        if (!empty($criteria['color'])) {
            $query->where('color', $criteria['color']);
        }

        if (!empty($criteria['width'])) {
            $query->where('width', $criteria['width']);
        }

        if (!empty($criteria['height'])) {
            $query->where('height', $criteria['height']);
        }

        if (!empty($criteria['price_min'])) {
            $query->where('price', '>=', $criteria['price_min']);
        }

        if (!empty($criteria['price_max'])) {
            $query->where('price', '<=', $criteria['price_max']);
        }

        if (!empty($criteria['sale_min'])) {
            $query->where('sale', '>=', $criteria['sale_min']);
        }

        if (!empty($criteria['sale_max'])) {
            $query->where('sale', '<=', $criteria['sale_max']);
        }

        if (!empty($criteria['number_of_items'])) {
            $query->limit($criteria['number_of_items']);
        }

        $products = $query->get();

        if ($products->isEmpty()) {
            return response()->json([
                'status' => 'empty',
                'message' => 'No products found matching the criteria from add to cart.',
                'products' => [],
            ]);
        }

        $data = [];

        foreach ($products as $product) {
            $data[] = [
                'id' => $product->id,
                'name' => $product->name,
                'url' => route('productDetails', $product->id),
                'category' => $product->category->name ?? 'Unknown',
                'brand' => $product->brand->name ?? 'Unknown',
                'color' => $product->color ?? null,
                'width' => $product->width ?? null,
                'height' => $product->height ?? null,
                'price' => number_format($product->price, 2),
            ];
        }

        return $data;
    }


    // checking prompt topic
    private function isProductInquiry(string $prompt): bool
    {
        // If no keywords match, use Gemini for a more intelligent check
        $query1 = "Suggest me products to use in my kitchen in white.";
        $query2 = "I need a large black sofa for my living room.";
        $query3 = "Show me some wooden tables under $500.";
        $query4 = "Find me some modern chairs in blue.";
        $query5 = "Recommend small office desks in brown.";
        $query6 = "What are the best glass coffee tables available?";
        $query7 = "I’m looking for beige rugs for my bedroom.";
        $query8 = "Give me a list of large metal shelves.";
        $query9 = "I need affordable dining tables between $100 and $400.";
        $query10 = "Show me some stylish floor lamps.";
        $query11 = "Suggest me some compact storage solutions for my bathroom.";
        $query12 = "Find soft fabric cushions for my couch.";
        $query13 = "Show me elegant chandeliers for my dining room.";
        $query14 = "Recommend some plastic chairs for outdoor use.";
        $query15 = "I need a medium-sized wooden bookshelf.";
        $query16 = "Find black and gold nightstands for my bedroom.";
        $query17 = "What are the best affordable office chairs?";
        $query18 = "Suggest some metal bar stools in red.";
        $query19 = "Show me elegant marble coffee tables under $600.";
        $query20 = "I need a large white wardrobe for my clothes.";
        $query21 = "Find me decorative mirrors in silver.";

        $geminiResponse = Gemini::generateText("Is this prompt asks about products by some products details (Note : It may ask about adding product to cart you should reply with no in this case)? Reply with 'yes' or 'no'.
         examples for prompts ($query1, $query2, $query3, $query4, $query5, $query6, $query7, $query8, $query9, $query10,
$query11, $query12, $query13, $query14, $query15, $query16, $query17, $query18, $query19, $query20, $query21.)
 and this is my Prompt: " . $prompt." Note: Categories should be places in home such as bedroom or kitchen and so on");
        $isRelated = strtolower(trim($geminiResponse)) === 'yes';
        return $isRelated;
    }




    public function addToCartCheck(string $prompt) : bool
    {
        $addToCart1 = "I want to add this product to my cart.";
        $addToCart2 = "Can you add a velvet armchair to my cart?";
        $addToCart3 = "Please add 2 floor lamps to my cart.";
        $addToCart4 = "I’d like to buy this table, add it to my cart.";
        $addToCart5 = "Add a ceramic vase set to my cart, please.";
        $addToCart6 = "I need to purchase this sofa, add it to my cart.";
        $addToCart7 = "Put 3 of these wall paintings in my cart.";
        $addToCart8 = "Can you add a large area rug to my cart?";
        $addToCart9 = "I want to check out with this chair, add it to my cart.";
        $addToCart10 = "Please include this bookshelf in my cart.";
        $addToCart11 = "Add this item to my cart, please.";
        $addToCart12 = "I’d like to place this product in my cart.";
        $addToCart13 = "Can you put this dining set in my cart?";
        $addToCart14 = "I need this lamp, please add it to my cart.";
        $addToCart15 = "Include this product in my shopping cart.";
        $addToCart16 = "Add 2 of these chairs to my cart.";
        $addToCart17 = "I want to buy this sofa, add it to my cart.";
        $addToCart18 = "Could you add this coffee table to my cart?";
        $addToCart19 = "I’d like to purchase this, add it to my cart.";
        $addToCart20 = "Place this home decor piece in my cart.";

        $check=Gemini::generateText("Is this prompt asks to add a product to cart? Reply with 'yes' or 'no'.
        examples for prompts ($addToCart1, $addToCart2, $addToCart3, $addToCart4, $addToCart5, $addToCart6, $addToCart7, $addToCart8, $addToCart9, $addToCart10, $addToCart11, $addToCart12, $addToCart13, $addToCart14, $addToCart15, $addToCart16, $addToCart17, $addToCart18, $addToCart19, $addToCart20) and this is my Prompt: ".$prompt);

        $isRelated = strtolower(trim($check)) === 'yes';
        return $isRelated;
    }


    public function addToFavoritesCheck(string $prompt): bool
    {
        $addToFav1 = "I want to add this product to my favorites.";
        $addToFav2 = "Can you add this chair to my wishlist?";
        $addToFav3 = "Please save this item to my favorites.";
        $addToFav4 = "I'd like to favorite this product.";
        $addToFav5 = "Mark this table as a favorite.";
        $addToFav6 = "Add this to my saved items.";
        $addToFav7 = "I want to keep this product in my wishlist.";
        $addToFav8 = "Can you add this lamp to favorites?";
        $addToFav9 = "Please include this item in my favorites.";
        $addToFav10 = "I want to bookmark this product.";
        $addToFav11 = "Save this sofa to my wishlist.";
        $addToFav12 = "Add this decor piece to my favorites.";
        $addToFav13 = "I'd like to keep this chair in favorites.";
        $addToFav14 = "Put this product in my wishlist.";
        $addToFav15 = "Please mark this item as a favorite.";
        $addToFav16 = "Add this coffee table to my saved products.";
        $addToFav17 = "I want to save this to favorites.";
        $addToFav18 = "Could you add this item to my wishlist?";
        $addToFav19 = "Include this in my favorites list.";
        $addToFav20 = "I’d like to favorite this for later.";

        $check = Gemini::generateText("Is this prompt asking to add a product to favorites or wishlist? Reply with 'yes' or 'no'.
Examples for prompts:
($addToFav1, $addToFav2, $addToFav3, $addToFav4, $addToFav5, $addToFav6, $addToFav7, $addToFav8, $addToFav9, $addToFav10,
$addToFav11, $addToFav12, $addToFav13, $addToFav14, $addToFav15, $addToFav16, $addToFav17, $addToFav18, $addToFav19, $addToFav20)
This is the user prompt: \"$prompt\"");

        $isRelated = strtolower(trim($check)) === 'yes';
        return $isRelated;
    }



    public function isServiceInquiry(string $prompt): bool
    {
        $example1 = "What services does your website offer?";
        $example2 = "Tell me what I can do on your platform.";
        $example3 = "What features does your home decor site include?";
        $example4 = "Can you explain the services you provide?";
        $example5 = "I want to know what your site offers.";
        $example6 = "What can I use your website for?";
        $example7 = "List the key features of your service.";
        $example8 = "Does your platform support 3D furniture previews?";
        $example9 = "Can I get help with product search on your site?";
        $example10 = "Is there an AI assistant on your website?";
        $example11 = "How does your shopping experience work?";
        $example12 = "What tools do you offer for sellers?";
        $example13 = "What kind of support do you give to buyers?";
        $example14 = "Can I see furniture in my room with your site?";
        $example15 = "What makes your platform different for home décor?";
        $example16 = "Do you offer any smart cart or wishlist features?";
        $example17 = "Tell me about your site’s core functionalities.";
        $example18 = "What’s included in your home decor services?";
        $example19 = "Does your site offer visual furniture previews?";
        $example20 = "What can I do if I’m a seller on your website?";

        $check = Gemini::generateText("Is this prompt asking about the services or features of the website? Reply with 'yes' or 'no'.
    Examples: ($example1, $example2, $example3, $example4, $example5, $example6, $example7, $example8, $example9, $example10, $example11, $example12, $example13, $example14, $example15, $example16, $example17, $example18, $example19, $example20).
    Now evaluate this user prompt: \"$prompt\"");

        return strtolower(trim($check)) === 'yes';
    }

}
