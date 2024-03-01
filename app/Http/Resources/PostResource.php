<?php

namespace App\Http\Resources;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    private $checkId;
    public function __construct(Post $resource, ?int $id){
        parent::__construct($resource);
        $this->checkId = $id;
    }

    public function toArray(Request $request): array
    {
        if($this->checkId){
            return [
                'id' => $this->id,
                'title' => $this->title,
                'image' => $this->image,
                'news_content' => $this->news_content,
                'created_at' => date('d-m-Y H:i:s', strtotime($this->created_at)),
                'user' => $this->whenLoaded('user'),
                'comments' => $this->whenLoaded('comments', function(){
                    return collect($this->comments)->each(function($comment){
                        $comment->user;
                        return $comment;
                    });
                }),
            ];
        }
        return [
            'id' => $this->id,
            'title' => $this->title,
            'image' => $this->image,
            'news_content' => $this->news_content,
            'created_at' => date('d-m-Y H:i:s', strtotime($this->created_at)),
            'user' => $this->whenLoaded('user'),
            'comments' => $this->whenLoaded('comments', function(){
                return collect($this->comments)->each(function($comment){
                    $comment->user;
                    return $comment;
                });
            }),
            'comments_total' => $this->whenLoaded('comments', function(){
                return $this->comments->count();
            }),
        ];
    }
}
