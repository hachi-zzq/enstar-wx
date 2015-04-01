<?php
namespace Enstar\Controller\Rest;

use Input;
use Response;
use Book;
use Lesson;
use Unit;
use Student;

/**
 * RestAPI 课本类
 * @author Hanxiang<hanxiang.qiu@enstar.com>
 */
class BookController extends BaseController
{
    /**
     * 获取所有课本
     * @author Hanxiang<hanxiang.qiu@enstar.com>
     */
    public function all()
    {
        $resultArray = array();
        $resultArray['books'] = null;
        $books = Book::where('status', 1)->get();
        if (count($books)) {
            $resultArray['books'] = array();
            foreach ($books as $key => $book) {
                $item = array();
                $item['id'] = $book->id;
                $item['book_key'] = $book->book_key;
                $item['name'] = $book->name;
                $item['title'] = $book->title;
                $item['subtitle'] = $book->subtitle;
                $item['description'] = $book->description;
                $item['cover'] = url($book->cover);
                $item['version'] = $book->version;
                $item['publisher'] = $book->publisher;
                $item['published_time'] = $book->publish_time;
                array_push($resultArray['books'], $item);
            }
        }
        $resultArray['count'] = count($books);

        return $this->encodeResult('10600', 'success', $resultArray);
    }

    /**
     * 根据课本 ID 获取课本
     * @author Hanxiang<hanxiang.qiu@enstar.com>
     * @param $id
     * @return mixed
     */
    public function byId($id)
    {
        $book = Book::find($id);
        if (!$book) {
            return $this->encodeResult('20701', 'empty set', array('book' => null, 'units' => null, 'lessons' => null));
        }

        $book = $this->getBookDetail($id);
        return $this->encodeResult('10700', 'success', $book);
    }

    /**
     * 根据课本标识符获取课本
     * @author Hanxiang<hanxiang.qiu@enstar.com>
     * @param $key
     * @return mixed
     */
    public function byKey($key)
    {
        $book = Book::where('book_key', $key)->first();
        if (!$book) {
            return $this->encodeResult('20702', 'empty set', array('book' => null, 'units' => null, 'lessons' => null));
        }

        $book = $this->getBookDetail($book->id);
        return $this->encodeResult('10701', 'success', $book);
    }

    private function getBookDetail($book_id)
    {
        $book = Book::find($book_id);
        $resultArray = array();

        // get book
        $bookItem = array();
        $bookItem['id'] = $book->id;
        $bookItem['book_key'] = $book->book_key;
        $bookItem['name'] = $book->name;
        $bookItem['title'] = $book->title;
        $bookItem['subtitle'] = $book->subtitle;
        $bookItem['description'] = $book->description;
        $bookItem['cover'] = url($book->cover);
        $bookItem['version'] = $book->version;
        $bookItem['publisher'] = $book->publisher;
        $bookItem['published_time'] = $book->publish_time;
        $resultArray['book'] = $bookItem;

        // get units
        $unitsArray = null;
        $units = Unit::where('book_id', $book_id)->orderBy('sort')->get();
        if (count($units)) {
            $unitsArray = array();
            foreach ($units as $key => $unit) {
                $unitItem = array();
                $unitItem['id'] = $unit->id;
                $unitItem['title'] = $unit->name;
                $unitItem['book_id'] = $unit->book_id;
                $unitItem['unit_unique'] = $unit->unit_unique;
                array_push($unitsArray, $unitItem);
            }
        }
        $resultArray['units'] = $unitsArray;

        // get lessons
        $lessonsArray = null;
        $lessons = Lesson::where('book_id', $book_id)->orderBy('unit_id')->orderBy('sort')->get();
        if (count($lessons)) {
            $lessonsArray = array();
            foreach ($lessons as $key => $lesson) {
                $lessonItem = array();
                $lessonItem['id'] = $lesson->id;
                $lessonItem['title'] = $lesson->title;
                $lessonItem['unit_id'] = $lesson->unit_id;
                $lessonItem['book_id'] = $lesson->book_id;
                $lessonItem['lesson_unique'] = $lesson->lesson_unique;
                array_push($lessonsArray, $lessonItem);
            }
        }
        $resultArray['lessons'] = $lessonsArray;

        return $resultArray;
    }
}
