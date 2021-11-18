<?php

function err($text, $payload = null) {
	/**
	 * @OA\Schema(
     *      schema="Error",
	 *		title="Error",
	 *		description="Base erroe",
	 *		@OA\Property(
	 *			property="error",
	 *			type="string",
	 *			description="Error mesage"
	 *		),
	 *		@OA\Property(
	 *			property="data",
	 *			type="object",
	 *			description="maybe payload about error",
	 *          default="null"
	 *		),
	 *		example={
	 *			"error": "Some error message",
	 *			"data": null
	 *		}
	 *    )
	 */
	return json_encode(
		array(
			'error' => $text,
			'data' => $payload
		)
	);
}

function jout($payload) {
	/**
	* @OA\Schema(
	*    schema="WithSuccess",
	*    title="WithSuccess",
	*    description="Response with success",
	*    @OA\Property(
	*        property="success",
	*        type="boolean",
	*        description="Признак успешности"
	*    ),
	*    example={
	*        "success": true
	*    }
	* )
	*/
	return json_encode($payload);
}


function data($payload) {
	return jout(array('data' => $payload));
}