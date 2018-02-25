<?php

namespace app\models;

use yii\base\Model;
use Yii;

class PaymentForm extends Model
{
    public $name;
    public $currency;
    public $summ;
    public $email;

    public static function tableName()
    {
        return 'payment';
    }

    public function rules()
    {
        return [
          [['name', 'currency', 'summ', 'email'], 'required'],
          ['email', 'email']
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Имя',
            'currency' => 'Валюта',
            'summ' => 'Сумма',
            'email' => 'Email'
        ];
    }

    /**
     * Отправляем письмо об оплате
     * @param $email
     * @return bool
     */
    public function send_payment($email)
    {
        if ($this->validate()) {
            Yii::$app->mailer->compose()
                ->setTo($this->email)
                ->setFrom($email)
                ->setSubject('You payment')
                ->setTextBody('You payment ' . $this->currency.$this->summ)
                ->send();

            return true;
        }
        return false;
    }

    /**
     * Пишем в базу оплату
     * @throws \yii\db\Exception
     */
    public function update()
    {
        $connection = Yii::$app->db;
        $connection->createCommand()->insert(self::tableName(),
            [
                'name' => $this->name,
                'email' => $this->email,
                'currency' => $this->currency,
                'summ' => $this->summ
            ])
            ->execute();

    }
}