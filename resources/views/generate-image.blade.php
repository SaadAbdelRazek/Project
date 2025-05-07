<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>مولد الصور بالذكاء الاصطناعي - ديكور إيطالي</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f4f0;
            color: #3e3e3e;
            background-image: url('https://images.unsplash.com/photo-1582582421323-3c9a5e099257?auto=format&fit=crop&w=1400&q=80');
            background-size: cover;
            background-position: center;
            backdrop-filter: blur(3px);
        }

        .overlay {
            background-color: rgba(255, 255, 255, 0.9);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }

        .container {
            background: #fff;
            padding: 2rem 3rem;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            text-align: center;
        }

        h2 {
            font-size: 24px;
            color: #843c0c;
            margin-bottom: 1rem;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 12px;
            font-size: 16px;
        }

        button {
            background-color: #c97a44;
            color: #fff;
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background-color: #a85e30;
        }

        img {
            margin-top: 2rem;
            max-width: 100%;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .error {
            color: red;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
<div class="overlay">
    <div class="container">
        <h2>توليد صورة ديكور بإلهام إيطالي</h2>

        @if(session('error'))
            <p class="error">{{ session('error') }}</p>
        @endif

        <form method="POST" action="{{ route('generate.image') }}">
            @csrf
            <input type="text" name="prompt" placeholder="مثال: مطبخ إيطالي بتفاصيل كلاسيكية" required>
            <button type="submit">توليد الصورة</button>
        </form>

        @if(isset($image))
            <h3>الصورة الناتجة:</h3>
            <img src="{{ $image }}" alt="Generated Italian-style decor">
        @endif
    </div>
</div>
</body>
</html>
