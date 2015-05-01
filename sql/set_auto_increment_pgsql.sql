SELECT MAX(id) FROM session;
SELECT nextval('session_id_seq');
SELECT setval('session_id_seq', (SELECT MAX(id) FROM session));

SELECT setval('document_id_seq', (SELECT MAX(id) FROM document));

SELECT setval('document_type_id_seq', (SELECT MAX(id) FROM document_type));

SELECT setval('interpret_id_seq', (SELECT MAX(id) FROM interpret));

SELECT setval('map_document_tag_id_seq', (SELECT MAX(id) FROM map_document_tag));

SELECT setval('schema_id_seq', (SELECT MAX(id) FROM schema));

SELECT setval('tag_id_seq', (SELECT MAX(id) FROM tag));

SELECT setval('user_id_seq', (SELECT MAX(id) FROM public.user));

SELECT setval('view_id_seq', (SELECT MAX(id) FROM view));
