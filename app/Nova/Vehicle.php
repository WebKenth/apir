<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\{ID,KeyValue,Code,Text,DateTime};
use Laravel\Nova\Http\Requests\NovaRequest;

class Vehicle extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Vehicle::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'plate';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'plate',
        'vin',
        'brand',
        'model'
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(__('ID'), 'id')->sortable(),
            Text::make('oid')->sortable(),
            Text::make('plate')->sortable(),
            Text::make('vin')->sortable(),
            Text::make('registration_status')->sortable(),
            Text::make('inspection_status')->sortable(),
            DateTime::make('registration_date')->sortable(),
            DateTime::make('inspection_date')->sortable(),
            Text::make('type')->sortable(),
            Text::make('usage')->sortable(),
            Text::make('model')->sortable(),
            Text::make('brand')->sortable(),
            Text::make('engine')->sortable(),
            Text::make('fuel_type')->sortable(),
            Code::make('raw')->json(),
            KeyValue::make('raw_dot'),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
