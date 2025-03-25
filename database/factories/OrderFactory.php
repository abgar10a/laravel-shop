<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $orderQuantity = $this->faker->numberBetween(1, 3);
        $article = Article::inRandomOrder()->first();
        $orderDate = $this->faker->dateTimeBetween('-3 months', '-1 months');
        $status = $this->faker->randomElement(OrderStatus::cases());

        return [
//            'user_id' => User::inRandomOrder()->first()->id,
            'article_id' => $article->id,
            'order_quantity' => $orderQuantity,
            'status' => $status,
            'price' => $article->price * $orderQuantity,
            'order_date' => $orderDate,
            'delivery_date' => $status === OrderStatus::COMPLETED ? $this->faker->dateTimeBetween($orderDate, 'now') : null,
        ];
    }
}
