<?php

namespace App\Http\Controllers;

use GeminiAPI\Laravel\Facades\Gemini;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GeminiController extends Controller
{
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


    public function handlePrompt(Request $request)
    {
        // Step 1: Get user input
        $userPrompt = $request->input('message');

        // Step 2: Send the prompt to Gemini for processing

        if ($this->isProductInquiry($userPrompt)) {
            $geminiResponse = $this->processWithGemini($userPrompt);

            // Step 3: Parse intent and entities
            $intent = $geminiResponse['intent'] ?? null;
            $entities = $geminiResponse['entities'] ?? [];

            Log::info('Gemini response:', (array)$geminiResponse);

            // Step 4: Check if the intent is related to home decoration
            if ($intent === 'Product Inquiry') {
                // Step 5: Fetch products based on extracted entities
                $productsHtml = $this->getHomeDecorProducts($entities);

                return response()->json([
                    'reply' => $productsHtml]);
            }

        }
        elseif ($this->addToCartCheck($userPrompt)) {
            $geminiResponse = $this->processWithGemini($userPrompt);

            // Step 3: Parse intent and entities
            $intent = $geminiResponse['intent'] ?? null;
            $entities = $geminiResponse['entities'] ?? [];

            Log::info('Gemini response:', (array)$geminiResponse);

            // Step 4: Check if the intent is related to home decoration
            if ($intent === 'Add Product To Cart') {
                // Step 5: Fetch products based on extracted entities
                $productsHtml = $this->getProductsToAddToCart($entities);

                return response()->json([
                    'rep' => "$productsHtml added to cart successfully!"]);
            }

            return response()->json([
                'repl' => 'Sorry, I can only help with home decoration inquiries.']);
        }
        else {
            $note1 = "If my message is greeting message you should reply normally ";

            $reply = Gemini::generateText("Check first if the following message is related to decoration and interior design if yes give me response (Note : Do not mention that my message is related to decoration and interior design in each time my message is related to them)
                                             and if not tell me sorry I cannot help you ! but $note1. my message is : " . $userPrompt);

            return response()->json(['reply' => $reply]);
        }

    }

    private function processWithGemini(string $s)
    {
        $geminiResponse1 = [
            'intent' => 'Product Inquiry',
            'entities' => [
                'category' => 'Bedroom',
                'color' => 'White',
                'size' => 'Medium',
                'price_min' => 100,
                'price_max' => 500,
                'number_of_items' => 3,
            ],
        ];

        $geminiResponse2 = [
            'intent' => 'Product Inquiry',
            'entities' => [
                'category' => 'Office',
                'color' => 'Black',
                'size' => 'Compact',
                'price_min' => 20,
                'price_max' => 150,
                'number_of_items' => 10,
            ],
        ];

        $geminiResponse3 = [
            'intent' => 'Product Inquiry',
            'entities' => [
                'category' => 'Kitchen',
                'color' => 'Red',
                'size' => 'Small',
                'price_min' => 30,
                'price_max' => 250,
                'number_of_items' => 7,
            ],
        ];

        $geminiResponse4 = [
            'intent' => 'Product Inquiry',
            'entities' => [
                'category' => 'Dining Room',
                'color' => 'Brown',
                'size' => 'Large',
                'price_min' => 200,
                'price_max' => 800,
                'number_of_items' => 2,
            ],
        ];

        $geminiResponse5 = [
            'intent' => 'Product Inquiry',
            'entities' => [
                'category' => 'Outdoor',
                'color' => 'Green',
                'size' => 'Extra Large',
                'price_min' => 150,
                'price_max' => 1000,
                'number_of_items' => 5,
            ],
        ];

        $geminiResponse6 = [
            'intent' => 'Product Inquiry',
            'entities' => [
                'category' => 'Bathroom',
                'color' => 'Gray',
                'size' => 'Standard',
                'price_min' => 40,
                'price_max' => 300,
                'number_of_items' => 6,
            ],
        ];

        $geminiResponse7 = [
            'intent' => 'Product Inquiry',
            'entities' => [
                'category' => 'Home Decor',
                'color' => 'Gold',
                'size' => 'Varied',
                'price_min' => 25,
                'price_max' => 150,
                'number_of_items' => 8,
            ],
        ];

        $geminiResponse8 = [
            'intent' => 'Product Inquiry',
            'entities' => [
                'category' => 'Living room',
                'color' => 'Beige',
                'size' => 'Large',
                'price_min' => 75,
                'price_max' => 400,
                'number_of_items' => 4,
            ],
        ];

        $geminiResponse9 = [
            'intent' => 'Product Inquiry',
            'entities' => [
                'category' => 'Lighting',
                'color' => 'Silver',
                'size' => 'Adjustable',
                'price_min' => 60,
                'price_max' => 350,
                'number_of_items' => 3,
            ],
        ];

        $geminiResponse10 = [
            'intent' => 'Product Inquiry',
            'entities' => [
                'category' => 'Rugs',
                'color' => 'Multicolor',
                'size' => 'Large',
                'price_min' => 80,
                'price_max' => 600,
                'number_of_items' => 2,
            ],
        ];

        $geminiResponse50 = [
            'intent' => 'Product Inquiry',
            'entities' => [
                'category' => 'Living Room',
                'color' => 'Blue',
                'size' => 'Large',
                'price_min' => 50,
                'price_max' => 200,
                'number_of_items' => 5,
            ],
        ];
        //----------------------------------------------------------------------------------------------
        $geminiResponse11 = [
            'intent' => 'Add Product To Cart',
            'entities' => [
                'name' => 'modern sofa',
                'quantity' => '1',
            ],
        ];

        $geminiResponse12 = [
            'intent' => 'Add Product To Cart',
            'entities' => [
                'name' => 'wooden dining table',
                'quantity' => '1',
            ],
        ];

        $geminiResponse13 = [
            'intent' => 'Add Product To Cart',
            'entities' => [
                'name' => 'LED floor lamp',
                'quantity' => '3',
            ],
        ];

        $geminiResponse14 = [
            'intent' => 'Add Product To Cart',
            'entities' => [
                'name' => 'decorative wall clock',
                'quantity' => '2',
            ],
        ];

        $geminiResponse15 = [
            'intent' => 'Add Product To Cart',
            'entities' => [
                'name' => 'cozy area rug',
                'quantity' => '1',
            ],
        ];

        $geminiResponse16 = [
            'intent' => 'Add Product To Cart',
            'entities' => [
                'name' => 'velvet armchair',
                'quantity' => '2',
            ],
        ];

        $geminiResponse17 = [
            'intent' => 'Add Product To Cart',
            'entities' => [
                'name' => 'marble coffee table',
                'quantity' => '1',
            ],
        ];

        $geminiResponse18 = [
            'intent' => 'Add Product To Cart',
            'entities' => [
                'name' => 'abstract painting',
                'quantity' => '1',
            ],
        ];

        $geminiResponse19 = [
            'intent' => 'Add Product To Cart',
            'entities' => [
                'name' => 'outdoor lounge chair',
                'quantity' => '2',
            ],
        ];

        $geminiResponse20 = [
            'intent' => 'Add Product To Cart',
            'entities' => [
                'name' => 'ceramic vase set',
                'quantity' => '3',
            ],
        ];

        $geminiResponse60 = [
            'intent' => 'Add Product To Cart', // Detected intent
            'entities' => [
                'name' => 'qui sovuir',
                'quantity' => '2',
            ],
        ];
        if ($this->isProductInquiry($s)) {
            $reply = Gemini::generateText('if the user only asks about product and do not want to add it to the cart you should change his prompt structure such as the following examples :' . json_encode($geminiResponse50)
                .json_encode($geminiResponse1)
                .json_encode($geminiResponse2)
                .json_encode($geminiResponse3)
                .json_encode($geminiResponse4)
                .json_encode($geminiResponse5)
                .json_encode($geminiResponse6)
                .json_encode($geminiResponse7)
                .json_encode($geminiResponse8)
                .json_encode($geminiResponse9)
                .json_encode($geminiResponse10). 'User prompt is :' . $s . ' note : if any of product properites is unspecified leave it empty');
        }
        elseif($this->addToCartCheck($s)){
            $reply = Gemini::generateText('if the message asks to add product to cart you should change his prompt structure as the following examples :' . json_encode($geminiResponse60). json_encode($geminiResponse11).
                json_encode($geminiResponse12).
                json_encode($geminiResponse13).
                json_encode($geminiResponse14).
                json_encode($geminiResponse15).
                json_encode($geminiResponse16).
                json_encode($geminiResponse17).
                json_encode($geminiResponse18).
                json_encode($geminiResponse19).
                json_encode($geminiResponse20). 'User prompt is :' . $s . ' note : if any of product properites is unspecified leave it empty and product name may consists of two or more syllables you should extract them');
        }

        return json_decode($reply, true);
    }

    public function getProductsToAddToCart(array $criteria)
    {
        // Build the query dynamically
        $quantity=1;
        $query = HomeDecorProduct::query();

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
            return '<p>No products found matching the criteria from add to cart.</p>';
        }

        $output = '<ul>';
        foreach ($products as $product) {
            $output .= '<li>';
            $output .= '<a  href="' . route('productDetails',htmlspecialchars($product->id)) . '"><strong>' . htmlspecialchars($product->name) . '</strong></a>';
            $output .= ' - Category: ' . htmlspecialchars($product->category);
            $output .= ', Color: ' . htmlspecialchars($product->color ?? 'N/A');
            $output .= ', Material: ' . htmlspecialchars($product->material ?? 'N/A');
            $output .= ', Size: ' . htmlspecialchars($product->size ?? 'N/A');
            $output .= ', Price: $' . number_format($product->price, 2);
            $output .= '</li>';

            $cartItem = Cart::updateOrCreate(
                [
                    'product_id' => $product->id,
                ],
                [
                    'quantity' => $quantity,
                    'price' => $product->price*$quantity,
                ]
            );
        }
        $output .= '</ul>';
        return $output;
    }

    // To use in getting products from database -----------------------------------
    public function getHomeDecorProducts(array $criteria)
    {
        // Build the query dynamically
        $query = HomeDecorProduct::query();

        // Check if each criterion exists in the array, and apply a conditional filter
        if (!empty($criteria['name'])) {
            $query->where('name', 'LIKE', '%' . $criteria['name'] . '%');
        }

        if (!empty($criteria['category'])) {
            $query->where('category', $criteria['category']);
        }

        if (!empty($criteria['color'])) {
            $query->where('color', $criteria['color']);
        }

        if (!empty($criteria['material'])) {
            $query->where('material', $criteria['material']);
        }

        if (!empty($criteria['size'])) {
            $query->where('size', $criteria['size']);
        }

        if (!empty($criteria['price_min'])) {
            $query->where('price', '>=', $criteria['price_min']);
        }

        if (!empty($criteria['price_max'])) {
            $query->where('price', '<=', $criteria['price_max']);
        }

        if (!empty($criteria['number_of_items'])) {
            $query->limit($criteria['number_of_items']);
        }

        // Execute the query and get the results
        $products = $query->get();

        // Format the results as an unordered list
        if ($products->isEmpty()) {
            return '<p>No products found matching the criteria.</p>';
        }

        $output = '<ul>';
        foreach ($products as $product) {
            $output .= '<li>';
            $output .= '<a  href="' . route('productDetails',htmlspecialchars($product->id)) . '"><strong>' . htmlspecialchars($product->name) . '</strong></a>';
            $output .= ' - Category: ' . htmlspecialchars($product->category);
            $output .= ', Color: ' . htmlspecialchars($product->color ?? 'N/A');
            $output .= ', Material: ' . htmlspecialchars($product->material ?? 'N/A');
            $output .= ', Size: ' . htmlspecialchars($product->size ?? 'N/A');
            $output .= ', Price: $' . number_format($product->price, 2);
            $output .= '</li>';
        }
        $output .= '</ul>';

        return $output;
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
}
