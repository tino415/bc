UPDATE map_document_tag d
INNER JOIN (
    SELECT a.id, (
        ((LOG(count) + 1)/ dtf1) * 
        (b.uniq / (1 + 0.115*b.uniq)) * 
        log(
            (
                (   
                    SELECT COUNT(*) 
                    FROM document
                ) - 
                (
                    SELECT COUNT(*) 
                    FROM map_document_tag 
                    WHERE tag_id = a.tag_id
                )
            ) / (
                SELECT COUNT(*) 
                FROM map_document_tag 
                WHERE tag_id = a.tag_id
            )
        )
    ) AS weight
    FROM map_document_tag AS a
    INNER JOIN (
        SELECT document_id, SUM(LOG(count) + 1) as dtf1, COUNT(*) AS uniq 
        FROM map_document_tag
        GROUP BY document_id
    ) AS b ON b.document_id = a.document_id
) AS e ON e.id = d.id
SET d.weight = e.weight
