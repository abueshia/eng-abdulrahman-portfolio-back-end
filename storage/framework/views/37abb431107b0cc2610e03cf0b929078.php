<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إشعار رسالة جديدة</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 20px;
            direction: rtl;
            text-align: right;
            color: #333333;
        }
        .container {
            max-width: 650px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid #e1e7ec;
        }
        .header {
            background: linear-gradient(135deg, #1e1e2d 0%, #151521 100%);
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0 0 8px 0;
            font-size: 22px;
            font-weight: 700;
            color: #ffffff;
        }
        .header p {
            margin: 0;
            font-size: 13px;
            color: #a1a5b7;
        }
        .badge {
            display: inline-block;
            padding: 6px 14px;
            font-size: 12px;
            font-weight: 700;
            border-radius: 20px;
            margin-top: 12px;
            background-color: #009ef7;
            color: #ffffff;
        }
        .badge-project {
            background-color: #50cd89;
        }
        .badge-job {
            background-color: #7239ea;
        }
        .badge-consultation {
            background-color: #ffc700;
            color: #1e1e2d;
        }
        .content {
            padding: 30px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .info-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #f1f1f4;
            font-size: 14px;
            vertical-align: top;
        }
        .info-table td.label {
            width: 32%;
            font-weight: 700;
            color: #5e6278;
            background-color: #f9f9fb;
        }
        .info-table td.value {
            color: #181c32;
            word-break: break-word;
        }
        .message-box {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-right: 4px solid #009ef7;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .message-box h3 {
            margin: 0 0 10px 0;
            font-size: 15px;
            color: #181c32;
        }
        .message-box p {
            margin: 0;
            font-size: 14px;
            line-height: 1.7;
            color: #3f4254;
            white-space: pre-wrap;
        }
        .action-container {
            text-align: center;
            margin: 30px 0 10px 0;
        }
        .btn-action {
            display: inline-block;
            padding: 14px 32px;
            background-color: #009ef7;
            color: #ffffff !important;
            text-decoration: none;
            font-weight: 700;
            font-size: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 158, 247, 0.35);
        }
        .footer {
            background-color: #f9f9fb;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #a1a5b7;
            border-top: 1px solid #f1f1f4;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>إشعار وارد جديد عبر الموقع</h1>
            <p>م. عبدالرحمن أبو عيشة - منصة إدارة المحتوى والبورتفوليو</p>
            <?php
                $typeClass = match($contactMessage->type?->value) {
                    'project_inquiry' => 'badge-project',
                    'job_offer' => 'badge-job',
                    'consultation' => 'badge-consultation',
                    default => '',
                };
            ?>
            <span class="badge <?php echo e($typeClass); ?>">
                <?php echo e($contactMessage->type?->label() ?? 'رسالة تواصل'); ?>

            </span>
        </div>

        <div class="content">
            <table class="info-table">
                <tr>
                    <td class="label">اسم المرسل:</td>
                    <td class="value"><strong><?php echo e($contactMessage->name); ?></strong></td>
                </tr>
                <tr>
                    <td class="label">البريد الإلكتروني:</td>
                    <td class="value">
                        <a href="mailto:<?php echo e($contactMessage->email); ?>" style="color: #009ef7; text-decoration: none; font-weight: 600;">
                            <?php echo e($contactMessage->email); ?>

                        </a>
                    </td>
                </tr>
                <?php if($contactMessage->phone): ?>
                    <tr>
                        <td class="label">رقم الهاتف / واتساب:</td>
                        <td class="value" dir="ltr" style="text-align: right;">
                            <a href="tel:<?php echo e($contactMessage->phone); ?>" style="color: #009ef7; text-decoration: none;">
                                <?php echo e($contactMessage->phone); ?>

                            </a>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php if($contactMessage->subject): ?>
                    <tr>
                        <td class="label">الموضوع:</td>
                        <td class="value"><?php echo e($contactMessage->subject); ?></td>
                    </tr>
                <?php endif; ?>
                <?php if($contactMessage->service): ?>
                    <tr>
                        <td class="label">الخدمة المطلوبة:</td>
                        <td class="value"><span style="color: #50cd89; font-weight: 600;"><?php echo e($contactMessage->service); ?></span></td>
                    </tr>
                <?php endif; ?>
                <?php if($contactMessage->budget_range): ?>
                    <tr>
                        <td class="label">الميزانية المقترحة:</td>
                        <td class="value"><?php echo e($contactMessage->budget_range); ?></td>
                    </tr>
                <?php endif; ?>
                <?php if($contactMessage->timeline): ?>
                    <tr>
                        <td class="label">الإطار الزمني:</td>
                        <td class="value"><?php echo e($contactMessage->timeline); ?></td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td class="label">تاريخ وتوقيت الإرسال:</td>
                    <td class="value" dir="ltr" style="text-align: right;"><?php echo e($contactMessage->created_at?->format('Y-m-d H:i:s') ?? now()->format('Y-m-d H:i:s')); ?></td>
                </tr>
            </table>

            <div class="message-box">
                <h3>نص الرسالة / تفاصيل الطلب:</h3>
                <p><?php echo e($contactMessage->message); ?></p>
            </div>

            <div class="action-container">
                <a href="<?php echo e($adminUrl); ?>" class="btn-action" target="_blank">
                    عرض الرسالة في لوحة التحكم الإدارية
                </a>
            </div>
        </div>

        <div class="footer">
            هذا إشعار تلقائي تم إرساله من خادم البورتفوليو الخاص بك فور تسجيل الطلب.
            <br>
            يمكنك الرد مباشرة على هذا البريد وسيتم توجيه الرد إلى صاحب الرسالة: <?php echo e($contactMessage->email); ?>.
        </div>
    </div>
</body>
</html>

<?php /**PATH D:\laragon\www\eng-abdulrahman-portfolio-back-end\resources\views/emails/new_contact_message.blade.php ENDPATH**/ ?>