<?php

namespace FNDE\Models;

class UsuarioSessao
{

    public function __construct
    (
        public readonly int $matricula,
        public readonly string $nome,
        public readonly string $se,
        public readonly string $perfil,
        public readonly string $usuarioLogado,
        public readonly string $horaLogin
    ) {}

    public static function fromArray(array $dados): self {
        
        return new self(
            matricula: $dados['matricula'],
            nome: $dados['nome'],
            se: $dados['se'],
            perfil: $dados['perfil'],
            usuarioLogado: $dados['usuarioLogado'],
            horaLogin: $dados['horaLogin']

        );
    }
}


?>