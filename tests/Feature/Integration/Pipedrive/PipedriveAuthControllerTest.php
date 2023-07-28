<?php

it('redirects to correct route', function () {
   signInAdmin();

   $this->get(route('authenticate.pipedrive.store'))->assertRedirect(route('custom-integration-fields.index'));
});
