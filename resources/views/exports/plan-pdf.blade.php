<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body { font-family: 'Arial', sans-serif; direction: rtl; text-align: right; }
        .header { text-align: center; margin-bottom: 30px; }
        .section { margin-bottom: 20px; border-bottom: 1px solid #ddd; padding-bottom: 10px; }
        .title { color: #2563eb; font-size: 18px; font-weight: bold; margin-bottom: 10px; }
        .content { color: #333; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $plan->title }}</h1>
        <p>{{ $plan->business_name }} - {{ $plan->industry }}</p>
    </div>

    @foreach($plan->sections as $section)
    <div class="section">
        <div class="title">{{ __('sections.' . $section->section_type) }}</div>
        <div class="content">
            {!! nl2br(e($section->content)) !!}
        </div>
    </div>
    @endforeach
</body>
</html>
