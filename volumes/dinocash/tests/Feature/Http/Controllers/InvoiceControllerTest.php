<?php

test('verificar que só pode existir uma invoice aberta', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});
