
@if ($status==false)

    <a href="{{ $url }}" class="btn btn-danger disabled {{ $size != 'none' ? "btn-{$size}" : '' }}"
       title="Você não tem permissão para '{{ $label }}'">
        <i class="{{ $icon or 'fas fa-times' }}"></i>
        <span class="d-none d-lg-inline">{{ $label }}</span>
    </a>

@else

    @php
        $btn_id = 'btn-delete-' . md5(microtime());
    @endphp

    <a id="{{ $btn_id }}" href="javascript:void(0)"
       class="btn btn-danger {{ $size != 'none' ? "btn-{$size}" : '' }} acl-action-delete"
       data-url="{{ $url }}" title="{{ $label }}">
        <i class="{{ $icon or 'fas fa-times' }}"></i>
        <span class="d-none d-lg-inline">{{ $label }}</span>
    </a>

    {{--
    O Código abaixo será renderizado apenas uma vez,
    quando o primeiro botão do tipo delete for invocado.
    --}}
    @php
        global $acl_delete_confirm_modal;

        if(isset($acl_delete_confirm_modal)) {
            $acl_delete_confirm_modal = false;
        } else {
            $acl_delete_confirm_modal = true;
        }
    @endphp

    @if($acl_delete_confirm_modal == true)

        <div id="js-acl-delete-confirm-modal-logic">

            @include('acl::modal-delete')

            <script>

                /*
                Ao final do carregamento do documento:
                1. Move a logica do modal de confirmação para o final do documento
                2. Gera uma instancia do objeto AclConfirmDelete e aplica o evento
                   de clique em todos os botões 'delete'.
                   O AclConfirmDelete é carregado em acl/src/resources/views/modal-delete.blade.php
                */

                function acl_attach_delete_confirm(elem) {
                    var confirm = new AclConfirmDelete();
                    confirm.debugMode({{ var_export(env('APP_DEBUG') || env('APP_ENV') === 'local') }});
                    confirm.setToken('{{ csrf_token() }}');
                    confirm.removeGridRow({{ $delete_row }});
                    confirm.attach(elem);
                }

                if (undefined === window.$) {

                    // Se jQuery ainda não estiver carregado
                    if(undefined === window.ready_acl_functions) {
                        window.ready_acl_functions = [];
                    }

                    window.ready_acl_functions.push(function(){
                        $('#js-acl-delete-confirm-modal-logic').appendTo("body");
                        $('.acl-action-delete').each(function(){
                            acl_attach_delete_confirm(this);
                        });
                    });

                    window.onload = function(){
                        $.each(window.ready_acl_functions, function(k, callback){
                            callback();
                        })
                    };
                    
                } else {
                    // se jQuery já estiver disponível
                    $(document).ready(function(){
                        $('#js-acl-delete-confirm-modal-logic').appendTo("body");
                        $('.acl-action-delete').each(function(){
                            acl_attach_delete_confirm(this);
                        });
                    });
                }

            </script>
        </div>
    @endif

@endif
