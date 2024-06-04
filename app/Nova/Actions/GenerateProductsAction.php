<?php

namespace App\Nova\Actions;

use App\Helpers\Classes\HandleGenerateProducts;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Http\Requests\NovaRequest;

class GenerateProductsAction extends Action
{
    use InteractsWithQueue, Queueable, HandleGenerateProducts;

    /**
     * Perform the action on the given models.
     *
     * @param ActionFields $fields
     * @param Collection $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        if (Hash::check(env('GENERATE_PRODUCTS_PASSWORD', 'YouMats@123'), $fields['password'])) {
            foreach ($models as $model) {
                $this->handleModels($model);
            }

            return Action::message('Products Generated Successfully');
        } else {
            throw new InvalidArgumentException('The given password is invalid.');
        }
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            Password::make('Password')->rules(REQUIRED_PASSWORD_NOT_CONFIRMED_VALIDATION),
        ];
    }
}