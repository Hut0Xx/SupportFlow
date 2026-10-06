<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class TicketResource extends JsonResource { public function toArray(Request $request): array { return ['id'=>$this->id,'code'=>$this->code,'title'=>$this->title,'description'=>$this->description,'status'=>$this->status,'resolution'=>$this->resolution,'requester'=>$this->whenLoaded('requester',fn()=>['id'=>$this->requester->id,'name'=>$this->requester->name]),'assignee'=>$this->whenLoaded('assignee',fn()=>['id'=>$this->assignee?->id,'name'=>$this->assignee?->name]),'team'=>$this->whenLoaded('team'),'category'=>$this->whenLoaded('category'),'priority'=>$this->whenLoaded('priority'),'sla'=>['due_at'=>$this->sla_due_at,'breached'=>$this->sla_due_at?->isPast() && !in_array($this->status,['resuelto','cerrado'])],'comments'=>$this->whenLoaded('comments'),'created_at'=>$this->created_at,'updated_at'=>$this->updated_at]; } }

