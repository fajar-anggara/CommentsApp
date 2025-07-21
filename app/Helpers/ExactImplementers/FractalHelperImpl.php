<?php

namespace App\Helpers\ExactImplementers;

use App\Helpers\Interfaces\FractalHelper;
use App\Transformers\UserTransformer;
use Illuminate\Contracts\Auth\Authenticatable;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\ArraySerializer;


class FractalHelperImpl implements FractalHelper
{

    /**
     * @var Manager Decide serializable and includes
     */
    protected Manager $manager;
    protected array $includes = [];
    protected Item $resource;

    public function useCommenterTransformer(Authenticatable $commenter, string $token = null): FractalHelper
    {
        $transformer = new UserTransformer();
        if ($token) {
            $transformer->setToken($token);
        }
        $this->resource = new Item($commenter, $transformer);

        return $this;
    }

    public function withIncludes(array $includes): FractalHelper
    {
        $this->includes = $includes;
        return $this;
    }

    public function buildWithArraySerializer(): array
    {
        $this->manager = (new Manager())->setSerializer(new ArraySerializer());
        if (count($this->includes) > 0) {
            $this->manager->parseIncludes($this->includes);
        }
        $resource = $this->resource;
        return $this->manager->createData($resource)->toArray();
    }
}
