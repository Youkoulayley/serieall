<?php
declare(strict_types=1);

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

/**
 * Class ShowsListTransformer
 * @package App\Http\Transformers
 */
class ShowsAbandonnedTransformer extends TransformerAbstract
{
    /**
     * @param $show
     * @return array
     */
    public function transform($show) : array
    {
        return [
            'id' => $show->id,
            'url' => '/serie/' . $show->show_url,
            'name' => $show->name];
    }
}