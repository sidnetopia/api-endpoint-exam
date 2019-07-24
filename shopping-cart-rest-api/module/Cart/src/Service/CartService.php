<?php
namespace Cart\Service;

class CartService
{
    public function __construct()
    {

    }

    /**
     * Decode jsonData from post and insert phone
     * by phoneId and jsonData. Return error if failed.
     *
     * @param mixed $data
     * @return ApiProblemResponse
     */
    public function create($data)
    {
        $data = $this->PhoneFilter->sanitize($data);
        $phoneId = $data['phoneId'];
        $jsonData = json_encode($data['jsonData']);

        try{
            $this->PhoneTable->insertPhone($phoneId, $jsonData);
        }catch (\Exception $e){
            return new ApiProblemResponse(new ApiProblem(500, 'Caught exception: '. $e->getMessage()));
        }

        return new ApiProblemResponse(new ApiProblem(201, 'Created'));
    }
}