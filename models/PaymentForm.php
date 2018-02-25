<?php

namespace app\models;



use yii\base\Model;
use Yii;
use yii\db\ActiveRecord;

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
        $this;
    }
}