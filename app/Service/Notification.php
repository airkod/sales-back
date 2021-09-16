<?php

namespace App\Service;

use App\Model\Additional;
use App\Model\Chat;
use App\Model\ChatRoom;
use App\Model\Course;
use App\Model\HomeTask;
use App\Model\Lesson;
use App\Model\Order;
use App\Model\Product;
use App\Model\Purchases;
use App\Model\Tariff;
use App\Model\Teacher;
use App\Model\TeacherNotification;
use App\Model\User;
use App\Model\Webinar;

use Light\Fcm;
use Light\Front;

use PHPMailer\PHPMailer\PHPMailer;

class Notification
{
    /**
     * @param Lesson $lesson
     * @throws \Light\Model\Exception\ConfigWasNotProvided
     */
    public static function startLesson(Lesson $lesson)
    {
        foreach (User::fetchAll() as $user) {

            if (Purchase::isCourseAvailable($user, Course::fetchOne())) {

                self::inAppNotification(
                    'Доступен новый урок: ' . $lesson->title,
                    \App\Model\Notification::DEST_LESSON,
                    $user,
                    $lesson->id
                );
            }
        }
    }

    /**
     * @param Lesson $lesson
     * @param User $user
     * @throws \Light\Model\Exception\ConfigWasNotProvided
     */
    public static function startLessonForUser(Lesson $lesson, User $user)
    {
        self::inAppNotification(
            'Доступен новый урок: ' . $lesson->title,
            \App\Model\Notification::DEST_LESSON,
            $user,
            $lesson->id
        );
    }

    /**
     * @param Webinar $webinar
     * @throws \Light\Model\Exception\ConfigWasNotProvided
     */
    public static function startWebinar(Webinar $webinar)
    {
        foreach (User::fetchAll() as $user) {

            if (Purchase::isWebinarAvailable($user, $webinar)) {

                self::inAppNotification(
                    'Через 30 минут начнется Вебинар: ' . $webinar->title,
                    \App\Model\Notification::DEST_WEBINAR,
                    $user
                );
            }
        }
    }

    /**
     * @param ChatRoom $chatRoom
     * @throws \Light\Model\Exception\ConfigWasNotProvided
     */
    public static function chatUserToTeacher(ChatRoom $chatRoom)
    {
        foreach (Teacher::fetchAll() as $teacher) {

            self::inAppNotificationTeacher(
                'У Вас новое собщение',
                TeacherNotification::DEST_CHAT,
                $teacher,
                $chatRoom->id
            );
        }
    }

    /**
     * @param ChatRoom $chatRoom
     * @throws \Light\Model\Exception\ConfigWasNotProvided
     */
    public static function chatTeacherToUser(ChatRoom $chatRoom)
    {
        self::inAppNotification(
            'У Вас новое сообщение.',
            \App\Model\Notification::DEST_CHAT,
            $chatRoom->user
        );
    }

    /**
     * @param User $user
     * @param string $password
     *
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public static function sendNewPassword(User $user, string $password)
    {
        self::sendMail(
            $user->email,
            $user->name,
            Front::getInstance()->getConfig()['email']['sender'][1] . '. Новый пароль',
            'Здравствуйте, ' . $user->name . ".\nВаш новый пароль - '" . $password . "'"
        );
    }

    /**
     * @param HomeTask $homeTask
     * @throws \Light\Model\Exception\ConfigWasNotProvided
     */
    public static function homeTaskIsReady(HomeTask $homeTask)
    {
        self::inAppNotification(
            'Ваше домашнее задание проверено.',
            \App\Model\Notification::DEST_HOME_TASK,
            $homeTask->user,
            $homeTask->lesson->id
        );
    }

    /**
     * @param HomeTask $homeTask
     *
     * @throws \Light\Model\Exception\ConfigWasNotProvided
     */
    public static function homeTaskIsAwaiting(HomeTask $homeTask)
    {
        foreach (Teacher::fetchAll() as $teacher) {

            self::inAppNotificationTeacher(
                'Необходимо проверить домашнее задание.',
                TeacherNotification::DEST_HOME_TASK,
                $teacher,
                $homeTask->id
            );
        }
    }

    /**
     * @param Order $order
     * @throws \Light\Model\Exception\ConfigWasNotProvided
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public static function paymentSuccessful(Order $order)
    {
        self::inAppNotification(
            'Бонусный счет успешно пополнен на ' . $order->bonus . ' бонусов',
            \App\Model\Notification::DEST_BILLING,
            $order->user
        );

        self::mail('Пополнение бонусного счета: ' . $order->user->name, [
            $order->user,
            'Пополнил на ' . $order->bonus . ' бонусов'
        ]);
    }

    /**
     * @param Purchases $purchases
     * @throws \Light\Model\Exception\ConfigWasNotProvided
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public static function coursePurchased(Purchases $purchases)
    {
        self::inAppNotification(
            'Курс успешно куплен. Занятия Вам будут доступны по мере их начала.',
            \App\Model\Notification::DEST_COURSE,
            $purchases->user
        );

        $tariff = Tariff::fetchOne([
            'id' => $purchases->data
        ]);

        self::mail('Куплен курс: ' . $purchases->user->name, [
            $purchases->user,
        ]);
    }

    /**
     * @param Purchases $purchase
     *
     * @throws \Light\Model\Exception\ConfigWasNotProvided
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public static function additionalPurchased(Purchases $purchase)
    {
        self::inAppNotification(
            'Дополнительный материал успешно куплен.',
            \App\Model\Notification::DEST_ADDITIONAL,
            $purchase->user,
            $purchase->data
        );

        $additional = Additional::fetchOne([
            'id' => $purchase->data
        ]);

        self::mail('Куплен ополнительный материал: ' . $purchase->user->name, [
            $purchase->user,
        ]);
    }

    /**
     * @param Purchases $purchase
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public static function productPurchased(Purchases $purchase)
    {
        $product = Product::fetchOne([
            'id' => $purchase->data
        ]);

        self::mail('Куплен продукт: ' . $purchase->user->name, [
            $purchase->user,
            'Продукт: ' . $product->title
        ]);
    }

    /**
     * @param string $content
     * @param string $destination
     * @param Teacher $teacher
     * @param string|null $data
     *
     * @throws \Light\Model\Exception\ConfigWasNotProvided
     */
    public static function inAppNotificationTeacher(string $content, string $destination, Teacher $teacher, string $data = null)
    {
        $notification = new \App\Model\TeacherNotification();

        $notification->dateTime = time();
        $notification->content = $content;
        $notification->destination = $destination;
        $notification->data = $data;

        $notification->save();

        $fcm = new Fcm;

        $fcm->setTokens([$teacher->token])
            ->setTitle(Front::getInstance()->getConfig()['fcm']['title'])
            ->setBody($content)
            ->setClickAction(str_replace('{$notification}', $notification->id, Front::getInstance()->getConfig()['fcm']['teacher-link']));

        $fcm->send();
    }

    /**
     * @param string $content
     * @param string $destination
     * @param User $user
     * @param string|null $data
     * @param bool $withFcm
     *
     * @throws \Light\Model\Exception\ConfigWasNotProvided
     */
    public static function inAppNotification(
        string $content,
        string $destination,
        User $user,
        string $data = null
    )
    {
        $notification = new \App\Model\Notification();

        $notification->dateTime = time();
        $notification->user = $user;
        $notification->content = $content;
        $notification->destination = $destination;
        $notification->data = $data;

        $notification->save();

        $fcm = new Fcm;

        $fcm->setTokens([$user->token])
            ->setTitle(Front::getInstance()->getConfig()['fcm']['title'])
            ->setBody($notification->content)
            ->setClickAction(str_replace('{$notification}', $notification->id, Front::getInstance()->getConfig()['fcm']['link']));

        $fcm->send();
    }

    /**
     * @param User $user
     * @return \App\Model\Notification[]
     */
    public static function getInAppNotifications(User $user)
    {
        return \App\Model\Notification::fetchAll(
            ['user' => $user, 'dateTime' => ['$lt' => time()]],
            ['dateTime' => -1]
        );
    }

    /**
     * @return \App\Model\Notification[]
     */
    public static function getInAppNotificationsTeacher()
    {
        return \App\Model\TeacherNotification::fetchAll(
            ['dateTime' => ['$lt' => time()]],
            ['dateTime' => -1]
        );
    }

    /**
     * @param User $user
     */
    public static function clearChatNotifications(User $user)
    {
        \App\Model\Notification::remove([
            'user' => $user,
            'destination' => \App\Model\Notification::DEST_CHAT,
        ]);
    }

    /**
     * @param ChatRoom $chatRoom
     */
    public static function clearChatNotificationsTeacher(ChatRoom $chatRoom)
    {
        foreach (Chat::fetchAll(['chatRoom' => $chatRoom, 'readTeacher' => false]) as $chat) {
            $chat->readTeacher = true;
            $chat->save();
        }
    }

    /**
     * @param Lesson $lesson
     * @param User $user
     *
     * @return bool
     */
    public static function isHomeTaskNotificationExists(Lesson $lesson, User $user)
    {
        return (bool)\App\Model\Notification::count([
            'user' => $user,
            'data' => $lesson->id,
            'destination' => \App\Model\Notification::DEST_HOME_TASK
        ]);
    }

    /**
     * @param Lesson $lesson
     * @param User $user
     *
     * @return bool
     */
    public static function isChatNotificationExists(Lesson $lesson, User $user)
    {
        return (bool)\App\Model\Notification::count([
            'user' => $user,
            'data' => $lesson->id,
            'destination' => \App\Model\Notification::DEST_CHAT
        ]);
    }

    /**
     * @param string $subject
     * @param array $body
     *
     * @return bool
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public static function mail(string $subject, array $body)
    {
        foreach ($body as $index => $value) {

            if ($body[$index] instanceof User) {
                $body[$index] = 'Пользователь: ' . $body[$index]->name . ' (' . $body[$index]->phone . ', ' . $body[$index]->email . ')' . ' https://admin.shkola-prodazh.online/user/manage?id=' . $body[$index]->id;
            }
        }
        $body = implode("\n", $body);

        $config = Front::getInstance()->getConfig()['email'];

        return self::sendMail($config['recipient'][0], $config['recipient'][1], $subject, $body);
    }

    /**
     * @param string $addressName
     * @param string $addressEmail
     * @param string $subject
     * @param string $body
     *
     * @return bool
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public static function sendMail(
        string $addressEmail,
        string $addressName,
        string $subject,
        string $body
    ) {
        $config = Front::getInstance()->getConfig()['email'];

        $mail = new PHPMailer(false);

        $mail->CharSet = PHPMailer::CHARSET_UTF8;

        $mail->isSMTP();
        $mail->Host = $config['smtp']['host'];
        $mail->Port = $config['smtp']['port'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->SMTPAuth = true;

        $mail->Username = $config['smtp']['username'];
        $mail->Password = $config['smtp']['password'];

        $mail->setFrom($config['sender'][0], $config['sender'][1]);
        $mail->addAddress($addressEmail, $addressName);

        $mail->Subject = $subject;
        $mail->Body = $body;

        return $mail->send();
    }
}
