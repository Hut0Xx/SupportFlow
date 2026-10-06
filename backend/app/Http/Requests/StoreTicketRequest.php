<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class StoreTicketRequest extends FormRequest { public function authorize(): bool { return $this->user()->can('create', \App\Models\Ticket::class); } public function rules(): array { return ['title'=>['required','string','max:180'],'description'=>['required','string','max:20000'],'category_id'=>['required',Rule::exists('categories','id')->where('organization_id',$this->user()->organization_id)],'priority_id'=>['required',Rule::exists('priorities','id')->where('organization_id',$this->user()->organization_id)],'attachments.*'=>['file','max:10240','mimes:pdf,png,jpg,jpeg,txt,log,zip']]; } }

