-- @todo transformer en DQL
SELECT a.id, a.title, (
    SELECT COUNT(c2.id)
    FROM Category c2
    INNER JOIN article_category ac2
    ON ac2.category_id = c2.id
    INNER JOIN Article a2
    ON a2.id = ac2.article_id
    WHERE a2.id = a.id
    AND c2.id IN (325,329,339)
) AS categoriesScore
FROM Article a
INNER JOIN article_category ac
ON ac.article_id = a.id
INNER JOIN Category c
ON c.id = ac.category_id
WHERE c.id IN (325,329,339)
GROUP BY a.id
ORDER BY categoriesScore DESC