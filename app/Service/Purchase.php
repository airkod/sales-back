<?php

namespace App\Service;

use App\Model\Additional;
use App\Model\Course;
use App\Model\Lesson;
use App\Model\Product;
use App\Model\Purchases;
use App\Model\Tariff;
use App\Model\User;

use App\Model\Webinar;
use Light\Config;
use Light\Exception;
use Light\Front;

class Purchase
{
    /**
     * @param User $user
     * @param Tariff $tariff
     *
     * @return bool
     *
     * @throws Exception
     * @throws \Light\Model\Exception\ConfigWasNotProvided
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public static function buyTariff(User $user, Tariff $tariff)
    {
        if ($tariff->price > $user->bonus) {
            throw new Exception([], 'У вас недостаточно бонусов для покупки курса');
        }

        $user->bonus = $user->bonus - $tariff->price;
        $user->save();

        $purchase = new Purchases();

        $purchase->user = $user;
        $purchase->bonus = $tariff->price;
        $purchase->product = Purchases::PRODUCT_TARIFF;
        $purchase->data = $tariff->id;
        $purchase->dateTime = time();

        $purchase->save();

        self::applyLessonToUser($user, Lesson::fetchOne(['enabled' => true], ['position' => 1]));

        Notification::coursePurchased($purchase);

        return true;
    }

    /**
     * @param User $user
     * @param Tariff $tariff
     *
     * @return bool
     *
     * @throws Exception
     * @throws \Light\Model\Exception\ConfigWasNotProvided
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public static function reBuyTariff(User $user, Tariff $tariff)
    {
        $existedTariff = self::getUserTariff($user);

        if ($tariff->price - $existedTariff->price > $user->bonus) {
            throw new Exception([], 'У вас недостаточно бонусов для покупки курса');
        }

        $user->bonus = $user->bonus - ($tariff->price - $existedTariff->price);
        $user->save();

        $purchase = new Purchases();

        $purchase->user = $user;
        $purchase->bonus = $tariff->price;
        $purchase->product = Purchases::PRODUCT_TARIFF;
        $purchase->data = $tariff->id;
        $purchase->dateTime = time();

        $purchase->save();

        $firstLesson = Lesson::fetchOne(['enabled' => true], ['position' => 1]);
        self::applyLessonToUser($user, $firstLesson);

        Notification::coursePurchased($purchase);

        return true;
    }

    /**
     * @param User $user
     * @param Lesson $lesson
     * @throws \Light\Model\Exception\ConfigWasNotProvided
     */
    public static function applyLessonToUser(User $user, Lesson $lesson)
    {
        if (self::isLessonAvailable($user, $lesson)) {
            return;
        }

        $availableLessons = $user->availableLessons;

        $availableLessons[] = [
            'id'   => $lesson->id,
            'date' => time()
        ];

        $user->availableLessons = $availableLessons;
        $user->save();

        Notification::startLessonForUser($lesson, $user);
    }

    /**
     * @param User $user
     * @param Additional $additional
     *
     * @throws Exception
     *
     * @throws \Light\Model\Exception\ConfigWasNotProvided
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public static function buyAdditional(User $user, Additional $additional)
    {
        if ($additional->price > $user->bonus) {
            throw new Exception([], 'У вас недостаточно бонусов для покупки');
        }

        $user->bonus = $user->bonus - $additional->price;
        $user->save();

        $purchase = new Purchases();

        $purchase->user = $user;
        $purchase->bonus = $additional->price;
        $purchase->product = Purchases::PRODUCT_ADDITIONAL;
        $purchase->data = $additional->id;
        $purchase->dateTime = time();

        $purchase->save();

        Notification::additionalPurchased($purchase);
    }

    /**
     * @param User $user
     * @param Product $product
     *
     * @throws Exception
     * @throws \Light\Model\Exception\ConfigWasNotProvided
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public static function buyProduct(User $user, Product $product)
    {
        if ($product->price > $user->bonus) {
            throw new Exception([], 'У вас недостаточно бонусов для покупки');
        }

        $user->bonus = $user->bonus - $product->price;
        $user->save();

        $purchase = new Purchases();

        $purchase->user = $user;
        $purchase->bonus = $product->price;
        $purchase->product = Purchases::PRODUCT_PRODUCT;
        $purchase->data = $product->id;
        $purchase->dateTime = time();

        $purchase->save();

        Notification::productPurchased($purchase);
    }

    /**
     * @param User $user
     * @param Course $course
     *
     * @return bool
     */
    public static function isCourseAvailable(User $user, Course $course)
    {
        $tariff = self::getUserTariff($user);

        if (!$tariff) {
            return false;
        }
        return true;
    }

    /**
     * @param User $user
     * @param Lesson $lesson
     *
     * @return bool
     */
    public static function isLessonAvailable(User $user, Lesson $lesson)
    {
        if ($user->carteBlanche) {
            return true;
        }

        foreach ($user->availableLessons as $availableLesson) {

            if ($availableLesson['id'] == $lesson->id) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param User $user
     * @return int
     */
    public static function getLastLessonStartDate(User $user)
    {
        $lastAvailableLesson = isset($user->availableLessons[count($user->availableLessons) - 1]) ? $user->availableLessons[count($user->availableLessons) - 1] : false;

        if ($lastAvailableLesson) {
            return $lastAvailableLesson['date'];
        }

        return 0;
    }

    /**
     * @param User $user
     * @throws \Light\Model\Exception\ConfigWasNotProvided
     */
    public static function applyNextLessonToUser(User $user)
    {
        // $lessons = Lesson::fetchAll(['enabled' => true], ['position' => 1]);

        $lesson = Lesson::fetchAll(['enabled' => true], ['position' => 1], 1, count($user->availableLessons));

        if (count($lesson)) {
            self::applyLessonToUser($user, $lesson[0]);
        }

//        foreach ($lessons as $index => $lesson) {
//
//            foreach ($user->availableLessons as $availableLesson) {
//
//                if ($availableLesson['id'] == $lesson->id) {
//
//                    if (isset($lessons[$index + 1])) {
//
//                    }
//                }
//            }
//        }
    }

    /**
     * @param User $user
     * @param Lesson $lesson
     * @return int
     */
    public static function whatDayTheLessonWillBeAvailable(User $user, Lesson $lesson)
    {
        $future = false;
        $futureLessonIndex = 0;
        $lastAvailableLessonStartDate = self::getLastLessonStartDate($user);
        $timeout = Front::getInstance()->getConfig()['course']['timeout'];

        foreach (Lesson::fetchAll(['enabled' => true], ['position' => 1]) as $index => $availableLesson) {

            if (!self::isLessonAvailable($user, $availableLesson)) {
                $future = true;
            }

            if ($future) {
                $futureLessonIndex ++;
            }

            if ($future && $availableLesson->id == $lesson->id) {
                return $lastAvailableLessonStartDate + ($timeout * $futureLessonIndex);
            }
        }

        return 0;
    }

    /**
     * @param User $user
     * @param Additional $additional
     *
     * @return bool
     */
    public static function isAdditionalAvailable(User $user, Additional $additional)
    {
        $tariff = self::getUserTariff($user);

        if ($tariff && $tariff->type == Tariff::EXPERT) {
            return true;
        }

        return (bool)Purchases::count([
            'user' => $user,
            'product' => Purchases::PRODUCT_ADDITIONAL,
            'data' => $additional->id,
            'dateTime' => ['$gt' => time() - Front::getInstance()->getConfig()['course']['additional']]
        ]);
    }

    /**
     * @param User $user
     * @param Webinar $webinar
     *
     * @return bool
     */
    public static function isWebinarAvailable(User $user, Webinar $webinar)
    {
        $tariff = self::getUserTariff($user);

        if ($tariff && $tariff->type != Tariff::BASE) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @return Tariff|bool|null
     */
    public static function getUserTariff(User $user)
    {
        if ($user->carteBlanche) {
            return Tariff::fetchOne(['type' => Tariff::BASE]);
        }

        $purchase = Purchases::fetchOne([
            'user' => $user,
            'product' => Purchases::PRODUCT_TARIFF
        ], ['dateTime' => -1]);

        if (!$purchase) {
            return false;
        }

        $expireTimestamp = Front::getInstance()->getConfig()['course']['expire'];

        if ($purchase->dateTime + $expireTimestamp < time()) {
            return false;
        }

        return Tariff::fetchOne(['id' => $purchase->data]);
    }

    /**
     * @param User $user
     * @return Purchases|null
     */
    public static function getUserCoursePurchase(User $user)
    {
        return Purchases::fetchOne([
            'user' => $user,
            'product' => Purchases::PRODUCT_TARIFF
        ], ['dateTime' => -1]);
    }

    /**
     * @param User $user
     *
     * @return Purchases[]
     */
    public static function get(User $user)
    {
        return Purchases::fetchAll([
            'user' => $user
        ]);
    }
}
